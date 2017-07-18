<?php

namespace core;
/**
 * Модель -- используя конфиг, как минимум подключается к базе данных и даёт 
 * потомкам работать с соединением
 */
class Model 
{
    /**
     * @var object Хранит соединение с БД
     */
    public $pdo;
   
    /**
     * @var string Имя обрабатываемой таблицы 
     */
    public $tableName = '';

    /**
     * Устанавливает соединение с БД
     * 
     * @return объект класса PDO для раборы с БД
     */
    public function __construct() 
    {
        $this->pdo = new \PDO(\Config::$db_dsn, \Config::$db_username,
                \Config::$db_password,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
 //       \DebugPrinter::debug($this, 'констр');
 //       echo ('Model constr!');
    }
   
    /**
     * Получает из БД все поля одной строки таблицы, с соответствующим Id
     * Возвращает объект класса модели
     * 
     * @param int $id
     * @return obgect
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM $this->tableName where id = :id";
//        \DebugPrinter::debug(self::$pdo);
//      
        $modelClassName = static::class;
        
        $st = $this->pdo->prepare($sql); 
//        \DebugPrinter::debug($st);
        
        $st -> bindValue( ":id", $id, \PDO::PARAM_INT );
        $st -> execute();
        $row = $st->fetch();
//        echo $modelClassName;
        if ( $row ) { return new $modelClassName( $row );}
    }
   
    /**
    * Возвращает все (или диапазон) объекты Article из базы данных
    *
    * @param int Optional Количество возвращаемых строк (по умолчанию = all)
    * @param int Optional Вернуть статьи только из категории с указанным ID
    * @param string Optional Столбц, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
    * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
    */
        
    public function getList( $numRows=1000000, $order="publicationDate DESC")
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName
                ORDER BY  $order  LIMIT :numRows";

        $modelClassName = static::class;
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue( ":numRows", $numRows, \PDO::PARAM_INT );
        $st->execute();
        $list = array();

        while ( $row = $st->fetch() ) {
            $article = new $modelClassName( $row );
            $list[] = $article;
        }

//         Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $this->pdo->query( $sql )->fetch();
        return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
    }

    /**
    * Устанавливаем свойства с помощью значений формы редактирования записи в заданном массиве
    *
    * @param assoc Значения записи формы
    */
    public function storeFormValues ( $params ) 
    {
        // Сохраняем все параметры
        $this->__construct( $params );

        // Разбираем и сохраняем дату публикации
        
        $date = preg_match('#(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d#', $params['publicationDate'], $matches);
        if (isset($matches)) {
        \DebugPrinter::debug($matches);
        }
        else {
            echo "Формат даты неверный";
        }
//        if ( isset($params['publicationDate']) ) {
//            $publicationDate = explode ( '-', $params['publicationDate'] );
//
//            if ( count($publicationDate) == 3 ) {
//                list ( $y, $m, $d ) = $publicationDate;
//                $this->publicationDate = mktime ( 0, 0, 0, $m, $d, $y );
//            }
//        }
    }
    
    /**
    * Удаляем текущий объект статьи из базы данных
    */
    public function delete() 
    {
        // Есть ли у объекта статьи ID?
//        if ( is_null( $this->id ) ) trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );

        // Удаляем статью
        $st = $this->pdo->prepare ( "DELETE FROM articles WHERE id = :id LIMIT 1" );
        $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
        $st->execute();
    }
}

