<?php 
    class QueryBuilder {
        private $conexion;
        private $tableName;
        private $columns = '*';
        private $where = '';
        private $orderBy = '';
        private $sql;
        private $limit = '';
        private $groupBy = '';
        private $joins = [];
        private $rawExpressions = [];

        public function __construct($conexion, $tableName)
        {
            $this->conexion = $conexion;
            $this->tableName = $tableName;
        }

        public function lastInsertId()
        {
            $result = mysqli_query($this->conexion, "SELECT LAST_INSERT_ID() as last_id");
            
            if (!$result) {
                die("Error al obtener el último ID insertado: " . mysqli_error($this->conexion));
            }

            $row = mysqli_fetch_assoc($result);
            return $row['last_id'];
        }

        // Método que permite obtener los datos de la base de datos.
        public function get() 
        {
            $this->sql = "SELECT $this->columns FROM $this->tableName";

            foreach($this->rawExpressions as $expression) {
                $this->sql .= " " . $expression;
            }

            $this->sql .= " $this->limit";

            $result = mysqli_query($this->conexion, $this->sql);

            if(!$result) {
                die("Error en la consulta: " . mysqli_error($this->conexion));
            }

            return $result;
        }


        // Método where para poder realizar condiciones.
        public function where($column, $value)
        {
            $value = mysqli_real_escape_string($this->conexion, $value);

            if(empty($this->where)) {
                $this->where = "WHERE $column = '$value'";
            } else {
                $this->where .= " AND $column = '$value'";
            }

            return $this;
        }

        
        // Método join que me permitira unier dos o más tablas.
        public function join($table, $column1, $operator, $column2, $alias = null) 
        {
            $join = [
                'table' => $table,
                'column1' => $column1,
                'operator' => $operator,
                'column2' => $column2
            ];

            if($alias) {
                $join['alias'] = $alias;
            }

            $this->joins[] = $join;
            return $this;
        }

        // Método que permite seleccionar los datos de la base de datos.
        public function select($columns) 
        {
            if (is_array($columns)) {
                $this->columns = implode(', ', $columns);
            } elseif ($columns === '*') {
                $this->columns = '*';
            } else {
                $this->columns = $columns;
            }

            $this->sql = "SELECT $this->columns FROM $this->tableName";

            foreach ($this->joins as $join) {
                $this->sql .= " INNER JOIN {$join['table']}";

                if (isset($join['alias'])) {
                    $this->sql .= " AS {$join['alias']}";
                }

                $this->sql .= " ON {$join['column1']} {$join['operator']} {$join['column2']}";
            }

            $this->sql .= " $this->where $this->orderBy $this->groupBy $this->limit";
            return $this;
        }


        // Método que me permite buscar por id.
        public function findById($id)
        {
            $this->sql = "SELECT $this->columns FROM $this->tableName WHERE id = '$id'";
            $result = mysqli_query($this->conexion, $this->sql);

            if(!$result) {
                die("La condición WHERE es requerida para buscar por id.");
            }

            return $this;
        }

        // Método que me permite buscar entre fechas.
        public function whereBetween($column, $start, $end)
        {
            $start = mysqli_real_escape_string($this->conexion, $start);
            $end = mysqli_real_escape_string($this->conexion, $end);

            $this->where .= empty($this->where) ? "WHERE $column BETWEEN '$start' AND '$end'" : " AND $column BETWEEN '$start' AND '$end'";
            return $this;
        }
        
        // Método que permite ordenar de forma ascendete
        public function orderBy($columns, $direction) 
        {
            if($this->orderBy === '') {
                $this->orderBy = " ORDER BY $columns $direction";
            } else {
                $this->orderBy .= ". $columns $direction";
            }

            return $this;
        }

        // Método que permite agrupar datos
        public function groupBy($columns)
        {
            if($this->groupBy === '') {
                $this->groupBy .= " GROUP BY $columns";
            }

            return $this;
        }

        // Método raw que permite agregar expresiones SQL sin escaparlas
        public function raw($expression) 
        {
            $this->rawExpressions[] = $expression;
            return $this;
        }

        // Método que permite insertar datos dentro de la base de datos.
        public function create($data) 
        {
            $columns = implode(', ', array_keys($data));
            $values = [];

            foreach($data as $key => $value) {
                if($value === 'NOW()') {
                    $values[] = 'NOW()';
                } else {
                    if(is_string($value)) {
                        $value = "'" . mysqli_real_escape_string($this->conexion, $value) . "'";
                    } else if(is_null($value)) {
                        $value = 'NULL';
                    }
                    $values[] = $value;
                }
            }

            $values = implode(', ', $values);
            $this->sql = "INSERT INTO $this->tableName ($columns) VALUES ($values)";

            $result = mysqli_query($this->conexion, $this->sql);
            if(!$result) {
                die("Error al intentar insertar los datos.");
            }

            return $this;
        }

        // Método para poder realizar la actualización por id de cada datos.
        public function update($data)
        {
            $setValues = [];

            foreach($data as $column => $value) {
                if($value === 'NOW()') {
                    $setValues[] = "$column = NOW()";
                } else {
                    if(is_string($value)) {
                        $value = "'" . mysqli_real_escape_string($this->conexion, $value). "'";
                    } else if(is_null($value)) {
                        $value = 'NULL';
                    }

                    $setValues[] = "$column = $value";
                }
            }

            $setValues = implode(', ', $setValues);

            if (!empty($this->where)) {
                $this->sql = "UPDATE $this->tableName SET $setValues $this->where";
                $result = mysqli_query($this->conexion, $this->sql);

                if(!$result) {
                    die("La condición WHERE es requerida para la actualización.");
                }

                return $this;
            } else {
                die("La condición WHERE es requerida para la actualización.");
            }
        }

        // Método que permite eliminar datos de la base de datos.
        public function delete()
        {
            if(!empty($this->where)) {
                $this->sql = "DELETE FROM $this->tableName " . $this->where;
                $result = mysqli_query($this->conexion, $this->sql);

                if(!$result) {
                    die("La condición WHERE es requerida para la eliminación.");
                }

                return $result;
            } else {
                die("La codición WHERE es requerida para la eliminación.");
            }
        }

        // Método que me permite crear LIMIT.
        public function limit($limit)
        {
            if(!is_numeric($limit) || $limit < 1) {
                die('Valor de limite inválido');
            }

            $this->limit = "LIMIT $limit";
            return $this;
        }
            
    }

?>