<div class="row mx-0">
    <?php use Astkon\Controllers\ArticleBalanceController;
    use Astkon\Controllers\ArticleCategoriesController;
    use Astkon\Controllers\ArticlesController;
    use Astkon\Controllers\BuildObjectsController;
    use Astkon\Controllers\MeasuresController;
    use Astkon\Controllers\UserGroupsController;
    use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <?php
            if (CURRENT_USER['IsAdmin']) {
                $menu = array(
                    array(
                        'Action' => '/' . ArticlesController::Name() . '/' . ArticlesController::Name() . 'List',
                        'Caption' => 'Артикулы',
                        'Icon' => '/icon-articles-dict.png',
                    ),
                    array(
                        'Action' => '/' . ArticleCategoriesController::Name() . '/' . ArticleCategoriesController::Name() . 'List',
                        'Caption' => 'Категории артикулов',
                        'Icon' => '/icon-categories.png',
                    ),
                    array(
                        'Action' => '/' . MeasuresController::Name() . '/' . MeasuresController::Name() . 'List',
                        'Caption' => 'Единицы измерения',
                        'Icon' => '/measures.png',
                    ),
                    array(
                        'Action' => '/' . ArticleBalanceController::Name() . '/' . ArticleBalanceController::Name() . 'List',
                        'Caption' => 'Текущий запас',
                        'Icon' => '/icon-balance.png',
                    ),
                    array(
                        'Action' => '/' . UserGroupsController::Name() . '/' . UserGroupsController::Name() . 'List',
                        'Caption' => 'Группы',
                        'Icon' => '/user-group-icon.png',
                    ),
                    array(
                        'Action' => '/' . BuildObjectsController::Name() . '/' . BuildObjectsController::Name() . 'List',
                        'Caption' => 'Объекты',
                        'Icon' => '/building.png',
                    ),
                    //            array(
                    //                'Action' => '/People/PeopleList',
                    //                'Caption' => 'Люди',
                    //                'Icon' => '/people.png',
                    //            ),

                );
            }
            else {
                $menu = array(
                    array(
                        'Action' => '/' . ArticleBalanceController::Name() . '/' . ArticleBalanceController::Name() . 'List',
                        'Caption' => 'Текущий запас',
                        'Icon' => '/icon-balance.png',
                    ),
                    array(
                        'Action' => '/' . ArticlesController::Name() . '/' . ArticlesController::Name() . 'List',
                        'Caption' => 'Артикулы',
                        'Icon' => '/icon-articles-dict.png',
                    ),
                    array(
                        'Action' => '/' . ArticleCategoriesController::Name() . '/' . ArticleCategoriesController::Name() . 'List',
                        'Caption' => 'Категории артикулов',
                        'Icon' => '/icon-categories.png',
                    ),
                    array(
                        'Action' => '/' . MeasuresController::Name() . '/' . MeasuresController::Name() . 'List',
                        'Caption' => 'Единицы измерения',
                        'Icon' => '/measures.png',
                    ),
//                    array(
//                        'Action' => '/UserGroups/UserGroupsList',
//                        'Caption' => 'Группы',
//                        'Icon' => '/user-group-icon.png',
//                    ),
//                    array(
//                        'Action' => '/BuildObjects/BuildObjectsList',
//                        'Caption' => 'Объекты',
//                        'Icon' => '/building.png',
//                    ),
                    //            array(
                    //                'Action' => '/People/PeopleList',
                    //                'Caption' => 'Люди',
                    //                'Icon' => '/people.png',
                    //            ),

                );
            }


        \Astkon\Lib\TileMenu($menu, 4, 2);
        ?>
    </div>
</div>

