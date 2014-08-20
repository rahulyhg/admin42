<?php
namespace Admin42\DataTable\Decorator;

use Admin42\DataTable\Column\Column;
use Zend\Json\Expr;

class DeleteButtonDecorator
{
    public function __invoke(Column $column)
    {
        $column->addAttribute("render", new Expr("dth.deleteButton"));
    }
}