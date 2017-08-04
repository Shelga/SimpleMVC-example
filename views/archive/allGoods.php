
<h2><?= $archivePageTitle ?></h2>

<div class="page">Страница
    <?php 
    $pagesCount = $archiveGood['totalRows']/$limit;
    if ($pagesCount !== (int)$pagesCount) {
        $pagesCount++;
    }
    $page = 1;
    while ($page <= $pagesCount) { ?>
        <span><a href="<?= \Url::link("archive/allGoods&pageNumber=$page") ?>"><?= $page ?></a></span>
        <?php $page++;
    } ?>
 </div>

<div>
<?php 
foreach ($archiveGood['results'] as $k => $v):?>
    <h4>
        <a href="<?= \Url::link("admin/good/index&id=". $archiveGood['results'][$k]->id)?>">
            <?= $archiveGood['results'][$k]->name; ?>
        </a>
    </h4>
    <p>Цена товара: <?= $archiveGood['results'][$k]->price; ?> р.
     В наличии: <?= $archiveGood['results'][$k]->available; ?> штук</p>
    <img src="/images/like1.png" height="20px" width="20px" data-modelId="<?= $archiveGood['results'][$k]->id?>" data-tableName='goods'>
        <span class="<?= $archiveGood['results'][$k]->id?>">
                <?= $archiveGood['results'][$k]->getModelLikes($archiveGood['results'][$k]->id, 'goods') ?>
        </span>
        <img id="loader-identity" src="/images/ajax-loader.gif" alt="gif">
    <hr><br>    
<?php endforeach; ?>
</div>
    
    
<div class="page">Страница
    <?php 
    $pagesCount = $archiveGood['totalRows']/$limit;
    if ($pagesCount !== (int)$pagesCount) {
        $pagesCount++;
    }
    $page = 1;
    while ($page <= $pagesCount) { ?>
        <span><a href="<?= \Url::link("archive/allGoods&pageNumber=$page") ?>"><?= $page ?></a></span>
        <?php $page++;
    } ?>
</div>
    
<!-- Это скрытый элемент для хранения данных, необходимых для реализайии загрузки при скроллинге страницы -->
<div style="display: none"
     id="loader-manager"
     data-url="/index.php?route=ajax/scrollingPage"
     data-limit="$limit"
     data-offset="0"

     data-parent-id="{$ParentId}"
     data-year="{$ProjectsLoadYear}" 
     data-parent-path="{$ParentPath}">
    <a href="#" class="link-new wide">тут можно что-то написать, если управлять по клику, но у нас элемент невидимый</a>
</div>

<div id='projects-container'></div>
