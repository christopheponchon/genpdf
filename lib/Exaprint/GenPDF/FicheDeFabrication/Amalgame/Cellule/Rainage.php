<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 18/02/2014
 * Time: 21:32
 */

namespace Exaprint\GenPDF\FicheDeFabrication\Amalgame\Cellule;


use Exaprint\GenPDF\FicheDeFabrication\Amalgame\ICellule;
use Exaprint\TCPDF\Cell;
use Exaprint\TCPDF\Color;
use Exaprint\TCPDF\FillColor;
use Exaprint\TCPDF\Font;
use Exaprint\TCPDF\Position;
use Exaprint\TCPDF\TextColor;

class Rainage implements ICellule
{
    public function draw(Position $position, \TCPDF $pdf, $cellSize, array $commande){
        if(isset($commande['Rainage']) && $commande['Rainage']){
            $cell = new Cell();
            $cell->position = $position;
            $cell->fillColor = new FillColor(Color::greyscale(255));
            $cell->fill = true;
            $cell->width = $cellSize;
            $cell->height = $cellSize;
            $cell->border = true;
            $cell->font = new Font('bagc-bold', 16, new TextColor(Color::greyscale(0)));
            $cell->align = Cell::ALIGN_CENTER;
            $cell->vAlign = Cell::VALIGN_CENTER;
            $cell->text = $commande['Rainage'];
            $cell->draw($pdf);
        } else {
            $cell = new Cell();
            $cell->position = $position;
            $cell->fill = false;
            $cell->width = $cellSize;
            $cell->height = $cellSize;
            $cell->border = true;
            $cell->draw($pdf);
        }
    }
}