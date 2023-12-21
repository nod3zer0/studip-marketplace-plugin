<?php

namespace TestPlugin;

use SimpleORMap;
use User;

class Test extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tp_texte';
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }

    public static function getTypes(): array
    {
        return [
            1 => 'Short story',
            2 =>  'novel'
        ];
    }

    public function getTypeDescription(): string
    {
        return self::getTypes()[$this->type] ?? 'Unknown type!';
    }
}
