<?php

namespace Astatroth\HexGrid;

/**
 * Class Grid
 *
 * @package Astatroth\HexGrid
 */
class Grid
{
    /**
     * @var int Size of the tile in pixels.
     */
    protected $tileSize = 100;

    /**
     * @var int The spacing between tiles in pixels.
     */
    protected $tileSpacing = 0;

    /**
     * @var bool Whether the grid must consist of pointy tiles.
     */
    protected $pointyTiles = false;

    /**
     * Calculates the axial distance between two cells.
     *
     * @param int $q1
     * @param int $r1
     * @param int $q2
     * @param int $r2
     * @return int
     */
    public function axialDistance(int $q1, int $r1, int $q2, int $r2):int
    {
        return (abs($q1 - $q2) + abs($r1 - $r2) + abs($q1 + $r1 - $q2 - $r2)) / 2;
    }

    /**
     * Converts axial coordinates into cube.
     *
     * @param array $axial
     * @return array
     */
    public function axialToCube(array $axial):array
    {
        return [
            'x' => $axial['q'],
            'y' => $axial['r'],
            'z' => -$axial['q'] - $axial['r']
        ];
    }

    /**
     * Converts cube coordinates into axial.
     *
     * @param array $cube
     * @return array
     */
    public function cubeToAxial(array $cube):array
    {
        return [
            'q' => $cube['x'],
            'r' => $cube['y']
        ];
    }

    /**
     * Returns x and y coordinates for a tile with the given coordinates, in pixels.
     *
     * @param int $q
     * @param int $r
     * @return array
     */
    public function getCenterXY(int $q, int $r):array
    {
        if ($this->pointyTiles) {
            $x = ($this->tileSize + $this->tileSpacing) * sqrt(3) * ($q + $r / 2);
            $y = -(($this->tileSize + $this->tileSpacing) * 3 / 2 * $r);
        } else {
            $x = ($this->tileSize + $this->tileSpacing) * 3 / 2 * $q;
            $y = -(($this->tileSize + $this->tileSpacing)) * sqrt(3) * ($q + $r / 2);
        }

        return [
            'x' => $x,
            'y' => $y
        ];
    }

    /**
     * Creates a hexagonal grid which consists of hexagonal cells.
     * Returns an array of coordinates.
     *
     * @param int  $q
     * @param int  $r
     * @param int  $radius
     * @param bool $solid
     * @return array
     */
    public function hexagon(int $q, int $r, int $radius, $solid = false):array
    {
        $result = [];

        if ($solid) {
            array_push($result, [
                'q' => $q,
                'r' => $r
            ]);
        }

        for ($currentRing = $i = 1;
             1 <= $radius ? $i <= $radius : $i >= $radius;
             $currentRing = 1 <= $radius ? ++$i : --$i) {
            $result = array_merge($result, $this->ring($q, $r, $currentRing));
        }

        return $result;
    }

    /**
     * Returns the array of coordinates of neighbors.
     *
     * @param int $q
     * @param int $r
     * @return array
     */
    public function neighbors(int $q, int $r):array
    {
        $result = [];
        $neighbors = [
            [1, 0],
            [1, -1],
            [0, -1],
            [-1, 0],
            [-1, 1],
            [0, 1]
        ];
        $length = count($neighbors);

        for ($i = 0; $i < $length; $i++) {
            $neighbor = $neighbors[$i];
            array_push($result, [
                'q' => $q + $neighbor[0],
                'r' => $r + $neighbor[1]
            ]);
        }

        return $result;
    }

    /**
     * Converts pixel coordinates into the decimal q and r.
     *
     * @param int $x
     * @param int $y
     * @param int $scale
     * @return array
     */
    public function pixelToDecimalQR(int $x, int $y, $scale = 1):array
    {
        if ($this->pointyTiles) {
            $q = (1 / 3 * sqrt(3) * $x - 1 / 3 * -$y) / ($this->tileSize + $this->tileSpacing);
            $r = 2 / 3 * -$y / ($this->tileSize + $this->tileSpacing);
        } else {
            $q = 2 / 3 * $x / ($this->tileSize + $this->tileSpacing);
            $r = (1 / 3 * sqrt(3) * -$y - 1 / 3 * $x) / ($this->tileSize + $this->tileSpacing);
        }

        $q /= $scale;
        $r /= $scale;

        return [
            'r' => $r,
            'q' => $q
        ];
    }

    /**
     * Creates a ring of cells.
     *
     * @param int $q
     * @param int $r
     * @param int $radius
     * @return array
     */
    public function ring(int $q, int $r, int $radius):array
    {
        $result = [];
        $moveDirections = [
            [1, 0],
            [0, -1],
            [-1, 0],
            [-1, 1],
            [0, 1],
            [1, 0],
            [1, -1]
        ];
        $length = count($moveDirections);

        for ($moveDirectionIndex = $i = 0; $i < $length; $moveDirectionIndex = ++$i) {
            $moveDirection = $moveDirections[$moveDirectionIndex];

            for ($j = 0, $ref = $radius - 1;
            (0 <= $ref ? $j <= $ref : $j >= $ref);
            (0 <= $ref ? $j++ : $j--)) {
                $q += $moveDirection[0];
                $r += $moveDirection[1];

                if ($moveDirectionIndex !== 0) {
                    array_push($result, [
                        'q' => $q,
                        'r' => $r
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Converts a floating point coordinates into integer.
     *
     * @param array $coordinates
     * @return array
     */
    public function roundCube(array $coordinates):array
    {
        $rx = round($coordinates['x']);
        $ry = round($coordinates['y']);
        $rz = round($coordinates['z']);
        $dx = abs($rx - $coordinates['x']);
        $dy = abs($ry - $coordinates['y']);
        $dz = abs($rz - $coordinates['z']);

        if ($dx > $dy && $dx > $dz) {
            $rx = -$ry - $rz;
        } else if ($dy > $dz) {
            $ry = -$rx - $rx;
        } else {
            $rz = -$rx - $ry;
        }

        return [
            'x' => $rx,
            'y' => $ry,
            'z' => $rz
        ];
    }
}