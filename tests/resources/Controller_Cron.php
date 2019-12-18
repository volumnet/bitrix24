<?php
/**
 * Контроллер Cron
 */
namespace RAAS;

/**
 * Файл контроллера Cron
 */
class Controller_Cron extends Abstract_Controller_Cron
{
    const lockFileExpiration = 7200;

    protected function fork()
    {
        $this->view = View_Web::i();
        parent::fork();
    }
}
