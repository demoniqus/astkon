<style type="text/css">

</style>

<div class="row mx-0">
    <?php
// https://professorweb.ru/my/html/html5/level5/5_4.php
    use Astkon\Controllers\ArticleCategoriesController;
    use Astkon\Controllers\ArticlesController;
    use Astkon\Controllers\MeasuresController;
    use Astkon\GlobalConst;
    use Astkon\Model\ArticleCategory;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="text-left">
            <input type="file" class="btn btn-outline-success" name="csvfile" accept="text/csv;text/plain" onchange="parseFile(this.files)">
            <a href="/<?= ArticlesController::Name(); ?>/<?= ArticlesController::Name(); ?>List" class="btn btn-outline-secondary ml-2">Артикулы</a>
        </div>
        <div class="alert alert-light my-2 text-left" style="font-size: 80%;">
            <p class="mb-1">Загрузите файл в формате CSV.</p>
            <p class="mb-1">Первой строкой должны идти ключи, по которым будет разобран файл:</p>
            <ol class="mb-1">
                <li>Article - наименование артикула. Не может быть пустым.</li>
                <li>Measure - наименование единицы измерения. Не может быть пустым. Если требуется использовать единицы измерения, уже имеющиеся в БД, название должно точно совпадать (в т.ч. регистр букв). Новые единицы измерения по умолчанию считаются неделимыми. При необходимости их можно настроить в <a href="<?= '/' . MeasuresController::Name() . '/MeasuresList' ; ?>">Единицы измерения</a></li>
                <li>Category - наименование категории, к которой относится артикул. Может быть пустым - в этом случае будет создана специальная категория без наименования. Если наименование совпадает с существующим в БД (в т.ч. регистр букв), категория не будет продублирована. Новые категории по умолчанию считаются расходуемыми и несписываемыми. При необходимости их можно настроить в <a href="<?= '/' . ArticleCategoriesController::Name() . '/ArticleCategoriesList' ; ?>">Категории</a></li>
                <li>VendorCode - код поставщика. Данный ключ не является обязательным при импорте.</li>
            </ol>
            <p class="mb-1">Порядок следования ключей произвольный. Порядок следования значений в последующих строках должен соответствовать порядку следования ключей.</p>
            <p class="mb-1">При импорте будут созданы все артикулы, отличающиеся наименованием, категорией или единицей измерения.</p>
        </div>
        <div class="alert processing-status my-2"></div>
        <div id="result">

        </div>
        <script type="application/javascript">
            function parseFile(file) {
                let processingStatusContainer = $('.processing-status');
                if (!window.FileReader) {
                    processingStatusContainer.addClass('alert-danger').html('Ваш браузер не поддерживает необходимые методы. Обновите версию браузера или установите браузер <a href="https://www.google.ru/chrome/" target="_blank">Google Chrome</a>');
                    return;
                }
                processingStatusContainer.removeClass('alert-success').removeClass('alert-danger').empty();
                if (!file[0]) {
                    return;
                }
                file = file[0];
                if (!/\.(?:txt|csv)$/i.test(file.name)) {
                    processingStatusContainer.addClass('alert-danger').html('Недопустимый тип файла');
                    return;
                }

                let outerContainer = $('#result');

                let reader = new FileReader();

                let categories = {};
                let measures = {};
                let articles = {};

                reader.onload = function(e){
                    let data = e.target.result;
                    let rows = data.split("\n");
                    window.importedItems = [];

                    let map = rows[0].split(';');
                    map = linq(map).toDict(function(key){ return key.replace(/^\s+/, '').replace(/\s+$/, '');}).collection;
                    if (!('Article' in map && 'Category' in map && 'Measure' in map)) {
                        processingStatusContainer.addClass('alert-danger').html('Файл не содержит информации о расположении ключевых полей Article, Category, Measure');
                        return;
                    }

                    map = rows[0].split(';')
                    for (let key in map) {
                        map[key] = map[key].replace(/^\s+/, '').replace(/\s+$/, '');
                        if (map[key] === 'Category') {
                            map[key] = '<?= ArticleCategory::Name(); ?>';
                        }
                    }

                    let rowIndex = 1;
                    while (rowIndex < rows.length) {
                        let _row = rows[rowIndex++].replace(/^\s+/, '').replace(/\s+$/, '');
                        if (!_row) {
                            continue;
                        }
                        let row = _row.split(';');
                        row = linq(row).select(function(rowItem){ return rowItem.replace(/^\s+/, '').replace(/\s+$/, '')}).collection;
                        let rowItemIndex = 0;
                        let article = {};
                        while(rowItemIndex < row.length) {
                            article[map[rowItemIndex]] = row[rowItemIndex].replace(/^\s+/, '').replace(/\s+$/, '');
                            rowItemIndex++;
                        }

                        if (!article.Article || !article.Measure) {
                            continue;
                        }

                        let articleKey = article.Article + '-' + article.Category + '-' + article.Measure

                        articles[articleKey] = article;
                        categories[article.<?= ArticleCategory::Name(); ?>] = {CategoryName: article.<?= ArticleCategory::Name(); ?>};
                        measures[article.Measure] = {MeasureName: article.Measure};
                    }

                    let article;

                    for (let key in articles) {
                        article = true;
                        break;
                    }
                    if (!article) {
                        processingStatusContainer.addClass('alert-danger').html('Нет ни одного артикула для импорта.');
                        return;
                    }

                    outerContainer.empty();

                    print(outerContainer, articles, categories, measures);

                    outerContainer.append('<button type="button" class="btn btn-success" onclick="save(this)">Импортировать...</button>');

                };
                reader.readAsText(file);

            }

            function print(/*Jquery DOM node*/ outerContainer, articles, categories, measures) {
                let container = $('<div class="row border border-secondary rounded py-2 mb-2"><div class="col-sm-1"><img src="/icon-categories.png" style="width: 48px; height: 48px;"/>' +
                    '<p>Категории</p></div><div class="col categories text-left offset-1"></div></div>');
                let categoriesContainer = container.find('.categories:first');
                linq(categories).foreach(function(category){categoriesContainer.append('<p>' + category.CategoryName + '</p>')});

                outerContainer.append(container);

                container = $('<div class="row border border-secondary rounded py-2 mb-2"><div class="col-sm-1"><img src="/measures.png" style="width: 48px; height: 48px;"/>' +
                    '<p>Единицы измерения</p></div><div class="col measures text-left offset-1"></div></div>');
                let measuresContainer = container.find('.measures:first');
                linq(measures).foreach(function(measure){measuresContainer.append('<p>' + measure.MeasureName + '</p>')});

                outerContainer.append(container);

                container = $('<div class="row border border-secondary rounded py-2 mb-2"><div class="col-sm-1"><img src="/barcode.png" style="width: 48px; height: 48px;"/>' +
                    '<p>Артикулы</p></div><div class="col text-left offset-1"><table class="articles table table-hover table-sm"></table></div></div>');
                let articlesContainer = container.find('.articles:first');

                let thead = $('<thead></thead>');
                let tr = $('<tr></tr>');
                tr.append('<th>Категория</th>');
                tr.append('<th>Наименование</th>');
                tr.append('<th>Единица измерения</th>');
                thead.append(tr);

                articlesContainer.append(thead);
                let tbody = $('<tbody></tbody>');
                linq(articles).foreach(function(article){
                    let tr = $('<tr></tr>');
                    tr.append('<td>' + article.<?= ArticleCategory::Name(); ?> + '</td>');
                    tr.append('<td>' + article.Article + '</td>');
                    tr.append('<td>' + article.Measure + '</td>');
                    tbody.append(tr);
                    tr.get(0).dataset.item = article;
                    window.importedItems.push(article);
                });
                articlesContainer.append(tbody);

                outerContainer.append(container);

            }

            function save(btn) {
                $(btn).attr('disabled', 'disabled').addClass('disabled');
                if (!window.importedItems.length) {
                    return;
                }
                let data = {
                    importedItems: importedItems
                };
                $.ajax({
                    url: '/<?= ArticlesController::Name(); ?>/Import',
                    data: data,
                    type: 'POST',
                    success: function(response){
                        if (response.errors) {
                            processingStatusContainer.addClass('alert-danger').html(response.errors.join('<br />'));
                            $(btn).removeAttr('disabled').removeClass('disabled');
                        }
                        else if (response.success) {
                            let outerContainer = $('#result');
                            outerContainer.empty();
                            outerContainer.append('<div class="row"><div class="col-4 offset-4 text-success mb-2">Импортировано</div></div>');
                            print(outerContainer, response.Article, response.ArticleCategory, response.Measure);

                        }
                    }
                });

            }
        </script>
    </div>
</div>
