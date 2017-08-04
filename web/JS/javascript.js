
var projectsContainerId = '#projects-container'; // контейнер, в который добалять вновь загруженные данные
var distanceFromBottomToStartLoad = 500; // в пикселях -- за сколько пикселей до конца страницы начинать загрузку
 
/* Элемент руководящей загрузкой - в его полях содержим все опции необходимые 
  для выборки очередной порции данных или прекращения загрузки */
var loaderManagerElementId = '#loader-manager'; // элемент, руководящий загрузкой
//var loadAjax = true;

$(function(){
    $('img').css('cursor', 'pointer');
    hideLoaderIdentity();
    actionLike();
    sessionLikesScore();
    
    initScrollingLoad(); // инициаллизируем обработчик прокрутки и фоновую загрузку
    formValidationOn();
});

var loadAjax = false;


function actionLike()
{
    $('img').on('click', function(){
        showLoaderIdentity();
        var modelId = $(this).attr('data-modelId');
        var table = $(this).attr('data-tableName');
        $.ajax({
            url: '/index.php?route=ajax/likes', 
            data: {id : modelId,
                   tableName : table},
            dataType: 'text',
        })
        .done (function(obj){
            hideLoaderIdentity();    
            console.log('Ответ получен');
            $('span.' + modelId).text(obj); 
            
        })
        .fail(function(){
            hideLoaderIdentity();
            console.log('Ошибка соединения с сервером');
        });
    });
}


function sessionLikesScore()
{
    setInterval(function(){
        showLoaderIdentity();
        $.ajax({
            url: '/index.php?route=ajax/sessionLikesCount', 
            dataType: 'text',
        })
        .done (function(obj){
            hideLoaderIdentity();    
            console.log('Ответ получен');
            $('#sessionLikesCount').text("Понравилось: " + obj); 
            
        })
        .fail(function(){
            hideLoaderIdentity();
            console.log('Ошибка соединения с сервером');
        });
    }, 5000);
}
    
function formValidationOn()
{
    $("#addUser").validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        }
    });
    
    $("#editUser").validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        }
    });
}

function searchGoods()
{
    
}

function initScrollingLoad()
{
    var $loadManager = $(loaderManagerElementId); 
    $(window).scroll(function() {
                
        // Проверяем пользователя, находится ли он в нижней части страницы

        if (($(window).scrollTop() + $(window).height() > $(document).height() - distanceFromBottomToStartLoad) && !loadAjax) {


            console.log('infinit load event!!');

            // Идет процесс
            loadAjax = true;

            // Сообщить пользователю что идет загрузка данных
            // $this.find('.loading-bar').html('Загрузка данных');  

            // Запустить функцию для выборки данных с установленной задержкой
            // Это полезно, если у вас есть контент в футере
            setTimeout(function() {

                var url =  $loadManager.data('url');
                var limit  =  $loadManager.data('limit');
                var offset  =  $loadManager.data('offset');
                
                var year  =  $loadManager.data('year');
                var parentPath   = $loadManager.data('parent-path');
                var parentId   = $loadManager.data('parent-id');

                $loadManager.data('offset', offset + limit); // сразу выставляем новое смещение для следующего запроса 

                var loadOptions = {
                    'limit':   limit ,
                    'offset': offset,
                    'year':  year,
                    'parent-path': parentPath,
                    'parent-id':  parentId
               }

                sendAjax(url, loadOptions); // передаём необходимые данные функции отправки запроса

            }, 30);

        }  
   });
}

function sendAjax(url, data)
{
    showLoaderIdenity(); //  показываем идентификатор загрузки
    $.ajax({ //  сам запрос
    type: 'POST',
    url: url,
    data: data, // данные которые передаём  серверу
    dataType: "text" //"json" // предполоижтельный формат ответа сервера
    }).done(function(res) { // если успешно
        hideLoaderIdenity(); // скрываем идентификатор загрузки

        appendHtml(res.html) // добавляем скаченные данные в конец ленты

        if (res.finished) { // если получили признак завершения прокрутки
            stopLoadTrying();
        }

        loadAjax = false; // укажем, что данный цикл загрузки завершён
        console.log('Ответ получен: ', res);

        if (res.success) { // если все хорошо
            console.log('ОК!)');

        } else { // если не нравится результат
            console.log('Пришли не те данные!');
            alert(res.message);
        }
    }).fail(function() { // если ошибка передачи
        hideLoaderIdenity();
        loadAjax = false;
        console.log('Ошибка выполнения запроса!');
    });
}

/**
 * Добавим подгруженные данные в ленту
 * 
 * @param {type} html
 * @returns {undefined}
 */
function appendHtml(html) {
    $(projectsContainerId).append(html);
}
 
 
function stopLoadTrying() {
  $(window).off('scroll'); // отвязываем обработку прокрутки от окна
}


