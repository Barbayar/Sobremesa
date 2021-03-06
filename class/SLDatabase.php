<?php
class SLDatabase
{
    private static $_instance = null;
    private static $_databasePath = __DIR__ . '/../data/database.sqlite';
    private static $_createTableQueries = array(
        'CREATE TABLE lunch (
            lunchId INTEGER PRIMARY KEY,
            userId INTEGER NOT NULL,
            theme TEXT NOT NULL,
            location TEXT NOT NULL,
            description TEXT NOT NULL,
            beginTime INTEGER NOT NULL,
            endTime INTEGER NOT NULL,
            minPeople INTEGER NOT NULL,
            maxPeople INTEGER NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            modifiedTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE comment (
            commentId INTEGER PRIMARY KEY,
            lunchId INTEGER NOT NULL,
            userId INTEGER NOT NULL,
            content TEXT NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            modifiedTime DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE member (
            lunchId INTEGER NOT NULL,
            userId INTEGER NOT NULL,
            createdTime DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (lunchId, userId)
        )',
        'CREATE TABLE user (
            userId INTEGER PRIMARY KEY,
            username TEXT NOT NULL,
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

        self::_createIndex('lunch', 'endTime');
        self::_createIndex('lunch', 'userId');
        self::_createIndex('comment', 'lunchId');
        self::_createIndex('member', 'userId');
        self::_createIndex('user', 'username', true);

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
