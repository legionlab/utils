<?php

namespace Llab\Utils;

/**
 * Control Pager
 *
 * Created by PhpStorm.
 * User: Leonardo Vilarinho
 * Date: 05/07/2016
 * Time: 10:55
 */
class Pager
{
    /**
     * @var int - numero de item por pagina, quantas paginas e o maximo de item que foi encontrado.
     */
    private $onePage = 5;
    private $page = 1;
    private $maximum = 0;

    /**
     * Pager constructor.
     * @param int $onePage - numero de item por pagina
     * @param int $maximum - maximo de item que foi encontrado
     */
    public function __construct($onePage = 5, $maximum = 0)
    {
        $this->onePage = $onePage;
        $this->maximum = $maximum;
    }

    /**
     * @return int - retorna o numero de paginas que a paginacao tem
     */
    public function numberPages()
    {
        return ceil($this->maximum / $this->onePage);
    }

    /**
     * Calcula um intervalo de item da pagina atual, exemplo: 0-5, 5-10.
     *
     * @return array - min o numero de inicio e max o numero final da pagina
     */
    public function range()
    {

        $this->page = $this->page > 0 ? $this->page : 1;

        $init = ($this->page - 1) * $this->onePage;

        return ['min' => $init, 'max' => $this->onePage ];
    }

    /**
     * Pega o maximo de itens.
     *
     * @return int - itens
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * Armazena um novo numero maximo.
     *
     * @param int $maximum - numero de itens
     * @return $this
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
        return $this;
    }

    /**
     * Pega o numero de itens a ser exibidos em uma pagina.
     *
     * @return int
     */
    public function getOnePage()
    {
        return $this->onePage;
    }

    /**
     * Armazena um numero de itens a ser exibido em uma pagina.
     *
     * @param int $onePage
     * @return $this
     */
    public function setOnePage($onePage)
    {
        $this->onePage = $onePage;
        return $this;
    }


    /**
     * Pega a pagina atual.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Altera a pagina atual.
     *
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        if(is_numeric($page))
            $this->page = $page;
        else
            $this->page = 1;
        return $this;
    }




}