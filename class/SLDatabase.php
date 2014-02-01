<?php
class SLDatabase
{
    private static $_instance = null;
    private static $_databasePath = '../data/database.sqlite';
    private static $_createTableQueries = array(
        'CREATE TABLE lunch (
            lunchId INTEGER PRIMARY KEY,
            theme TEXT NOT NULL,
            location TEXT NOT NULL,
            description TEXT NOT NULL,
            beginTime DATETIME NOT NULL,
            endTime DATETIME NOT NULL,
            minPeople INTEGER NOT NULL,
            maxPeople INTEGER NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            modifiedTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE comment (
            commentId INTEGER PRIMARY KEY,
            userId INTEGER NOT NULL,
            lunchId INTEGER NOT NULL,
            content TEXT NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            modifiedTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE member (
            id INTEGER PRIMARY KEY,
            lunchId INTEGER NOT NULL,
            userId INTEGER NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE user (
            userId INTEGER PRIMARY KEY,
            userName TEXT NOT NULL,
            email TEXT NOT NULL,
            displayName TEXT NOT NULL,
            data TEXT NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            modifiedTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
    );

    private static function _createIndex($table, $column, $unique = false)
    {
        $indexName = str_replace(',', '', $column) . "IndexOn$table";
        self::$_instance->query(
            'CREATE ' . ($unique ? 'UNIQUE ' : '') . "INDEX $indexName ON $table ($column)"
        );
    }

    private static function _createModifiedTimeTrigger($table, $primaryKey)
    {
        $triggerName = "modifiedTimeTriggerOn$table";
        self::$_instance->query(
            "CREATE TRIGGER $triggerName AFTER UPDATE ON $table
            BEGIN
                UPDATE $table SET modifiedTime = CURRENT_TIMESTAMP WHERE $primaryKey = new.$primaryKey;
            END"
        );
    }

    private static function _createDatabase()
    {
        self::$_instance = new medoo(array(
            'database_type' => 'sqlite',
            'database_file' => self::$_databasePath,
            'charset' => 'utf8',
        ));

        foreach (self::$_createTableQueries as $createTableQuery) {
            self::$_instance->query($createTableQuery);
        }

        self::_createIndex('lunch', 'beginTime');
        self::_createIndex('comment', 'lunchId');
        self::_createIndex('member', 'lunchId');
        self::_createIndex('member', 'userId,lunchId', true);
        self::_createIndex('user', 'userName', true);

        self::_createModifiedTimeTrigger('lunch', 'lunchId');
        self::_createModifiedTimeTrigger('comment', 'commentId');
        self::_createModifiedTimeTrigger('user', 'userId');

        $error = self::$_instance->error();

        if (!is_null($error[2])) {
            unlink(self::$_databasePath);
            throw new Exception("Error: $error[2]");
        }
    }

    public static function getInstance()
    {
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }

        if (!file_exists(self::$_databasePath)) {
            self::_createDatabase();

            return self::$_instance;
        }

        self::$_instance = new medoo(array(
            'database_type' => 'sqlite',
            'database_file' => self::$_databasePath,
            'charset' => 'utf8',
        ));

        return self::$_instance;
    }
}
