<?php

namespace Kanboard\Plugin\StarredProjects\Schema;

use PDO;

const VERSION = 1;

function version_1(PDO $pdo)
{
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS starred_projects (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT(11) NOT NULL,
          project_id INT(11) NOT NULL,
          UNIQUE(user_id, project_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ');
}
