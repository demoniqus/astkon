<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 15:47
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\View\View;

class OperationsController extends Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function IndexAction(array $context) {
        (new View())->generate();
    }

    /**
     * Форма прихода на баланс
     * @param array $context
     */
    public function IncomeFormAction(array $context){

    }

    /**
     * Форма расхода с баланса
     * @param array $context
     */
    public function ExpenditureFormAction(array $context) {

    }

    /**
     * Форма безвозвратного безвозмездного списания
     * @param array $context
     */
    public function WriteoffFormAction(array $context) {

    }

    /**
     * Форма передачи во временное безвозмездное пользование
     * @param array $context
     */
    public function FreeRentFormAction(array $context) {

    }
}