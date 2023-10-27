<?php
    include_once "./libs/orm/query-builder.php";

    class DatabaseConnection {
        private $conexion;

        public function __construct($conexion)
        {
            $this->conexion = $conexion;
        }

        public function getConexion()
        {
            return $this->conexion;
        }

        public function table($tableName) 
        {
            return new QueryBuilder($this->conexion, $tableName);
        }
    }
?>
