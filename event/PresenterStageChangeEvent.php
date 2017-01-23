<?php

namespace Wame\Core\Event;

use Nette\Application\UI\Presenter;
use Nette\InvalidArgumentException;

class PresenterStageChangeEvent
{
    const stages = ['startup', 'action', 'signal', 'render', 'terminate'];


    /** @var Presenter */
    private $presenter;

    /** @var string */
    private $stage;


    /**
     * 
     * @param Presenter $presenter
     * @param string $stage
     */
    public function __construct(Presenter $presenter, $stage)
    {
        if (!in_array($stage, self::stages)) {
            throw new InvalidArgumentException("Invalid presenter stage $stage.");
        }
        $this->presenter = $presenter;
        $this->stage = $stage;
    }


    /**
     * Returns whenever it is event entering stage
     * 
     * @param string $stage
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function enters($stage)
    {
        if (!in_array($stage, self::stages)) {
            throw new InvalidArgumentException("Invalid presenter stage $stage.");
        }
        return $this->stage == $stage;
    }

    /**
     * Returns whenever it is event leaving stage
     * 
     * @param string $stage
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function leaves($stage)
    {
        if (!in_array($stage, self::stages)) {
            throw new InvalidArgumentException("Invalid presenter stage $stage.");
        }
        return array_search($stage, self::stages) == array_search($this->stage, self::stages) - 1;
    }

    /**
     * Current presenter
     * 
     * @return Presenter
     */
    function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * Current stage
     * 
     * @return string
     */
    function getStage()
    {
        return $this->stage;
    }

}
