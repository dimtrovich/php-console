<?php

namespace BlitzPHP\Console\Overrides;

use Ahc\Cli\Output\Cursor as AhcCursor;

class Cursor extends AhcCursor
{
    /**
     * Cache le curseur
     */
    public function hide(): string
    {
        return "\e[?25l";
    }

    /**
     * Affiche le curseur
     */
    public function show(): string
    {
        return "\e[?25h";
    }

    /**
     * Va à une colonne spécifique
     */
    public function col(int $col): string
    {
		return $col >= 0 ? $this->right($col) : $this->left(abs($col));
    }

    /**
     * Positionne le curseur
     */
    public function position(int $row, int $col): string
    {
        return "\e[{$row};{$col}H";
    }

    /**
     * Sauvegarde la position du curseur
     */
    public function save(): string
    {
        return "\e[s";
    }

    /**
     * Restaure la position du curseur
     */
    public function restore(): string
    {
        return "\e[u";
    }
}
