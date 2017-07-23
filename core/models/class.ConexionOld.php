<?php
/**
 * 	Class Conexion
 *
 * @category  Acceso a Base de Datos
 * @package   Conexion
 * @author    Luis Candelario
 * @copyright Copyright (c) 2017
 * @version   1.0 testing
 * https://github.com/joshcam/PHP-MySQLi-Database-Class/blob/master/MysqliDb.php
 */




class Conexion
{
    /**
     * Instancia estática de sí mismo 
     * @var Conexion
     */
    protected static $_instance;
    /**
     * Prefijo de la Tabla
     * @var string
     */
    public static $prefix = '';
    /**
     * Instancia MySQLi
     * @var mysqli[]
     */
    protected $_mysqli = [];
    /**
     * La consulta SQL para ser preparada y ejecutada 
     * @var string
     */
    protected $_query;
    /**
     * La consulta SQL ejecutada anteriormente 
     * @var string
     */
    protected $_lastQuery;
    /**
     * Las opciones de consulta SQL requeridas después de SELECT, INSERT, UPDATE o DELETE 
     * @var string
     */
    protected $_queryOptions = array();
    /**
     * Un array que contiene joins
     * @var array
     */
    protected $_join = array();
    /**
     * Un array que contiene las condiciones 
     * @var array
     */
    protected $_where = array();
    /**
     * Un array que contiene los join ands
     *
     * @var array
     */
    protected $_joinAnd = array();
    /**
     * Un array que tiene condiciones 
     * @var array
     */
    protected $_having = array();
    /**
     * Lista de tipos dinámicos para ordenar por el valor de la condición
     * @var array
     */
    protected $_orderBy = array();
    /**
     * Lista de tipos dinámicos para el valor de group by por condición 
     * @var array
     */
    protected $_groupBy = array();
	
	/**
	 * Lista de tipos dinámicos para tablas de bloqueo de temporal.  
	 * @var array
	 */
	protected $_tableLocks = array();
	
	/**
	 * Variable que contiene el método de bloqueo de tabla actual.
	 * @var string
	 */
	protected $_tableLockMethod = "READ";
	
    /**
     * Array dinámico que contiene una combinación de los tipos de valores de datos condición / tabla y referencias de parámetros
     * @var array
     */
    protected $_bindParams = array(''); // Crear el índice 0 vacío 
    /**
     * Variable que contiene una cantidad de filas devueltas durante las consultas get / getOne / select 
     * @var string
     */
    public $count = 0;
    /**
     * Variable que contiene una cantidad de filas devueltas durante get / getOne / select consultas con withTotalCount () 
     * @var string
     */
    public $totalCount = 0;
    /**
     * Variable que contiene el error de la última sentencia 
     * @var string
     */
    protected $_stmtError;
    /**
     * Variable que contiene el último código de error de la sentencia 
     * @var int
     */
    protected $_stmtErrno;
    /**
     * Es objeto Subquery 
     * @var bool
     */
    protected $isSubQuery = false;
    /**
     * Nombre de la columna de incremento automático 
     * @var int
     */
    protected $_lastInsertId = null;
    /**
     * Nombres de columnas para la actualización cuando se utiliza el método onDuplicate 
     * @var array
     */
    protected $_updateColumns = null;
    /**
     * Tipo de retorno: 'array' para devolver resultados como array, 'objeto' como objeto 
 	 * 'Json' como cadena json 
     * @var string
     */
    public $returnType = 'array';
    /**
     * Los resultados de join () deben ser anidados por tabla 
     * @var bool
     */
    protected $_nestJoin = false;
    /**
     * Nombre de la tabla (con prefijo, si se utiliza)
     * @var string 
     */
    private $_tableName = '';
    /**
     * DE ACTUALIZACIÓN flag
     * @var bool
     */
    protected $_forUpdate = false;
    /**
     * BLOQUEO EN MODO DE COMPARTIR flag
     * @var bool
     */
    protected $_lockInShareMode = false;
    /**
     * Campo clave para Map () 'ed array de resultados 
     * @var string
     */
    protected $_mapKey = null;
    /**
     * Variables para el seguimiento de la ejecución de consultas 
     */
    protected $traceStartQ;
    protected $traceEnabled;
    protected $traceStripPrefix;
    public $trace = array();
    /**
     * Por página límite de paginación 
     *
     * @var int
     */
    public $pageLimit = 20;
    /**
     * Variable que contiene el número total de páginas de la última consulta paginate () 
     *
     * @var int
     */
    public $totalPages = 0;
    /**
     * @var  configuraciones de conexiones de array [profile_name=>[same_as_contruct_args]]
     */
    protected $connectionsSettings = [];
    /**
     * @var string con el nombre de una conexión predeterminada mysqli (principal) 
     */
    public $defConnectionName = 'default';


     /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $db
     * @param int $port
     * @param string $charset
     * @param string $socket
     */
    public function __construct($host = DB_HOST, $username = DB_USER, $password = DB_PASS, $db = DB_NAME, $port = null, $charset = 'utf8', $socket = null)
    {
        $isSubQuery = false;
        // si los parámetros se pasaron como array 
        if (is_array($host)) {
            foreach ($host as $key => $val) {
                $$key = $val;
            }
        }
        $this->addConnection('default', [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'db' => $db,
            'port' => $port,
            'socket' => $socket,
            'charset' => $charset
        ]);
        if ($isSubQuery) {
            $this->isSubQuery = true;
            return;
        }
        if (isset($prefix)) {
            $this->setPrefix($prefix);
        }
        self::$_instance = $this;
    }


    /**
     * Un método para conectarse a la base de datos
     *
     * @param null|string $connectionName
     * @throws Exception
     * @return void
     */
    public function connect($connectionName)
    {
        if(!isset($this->connectionsSettings[$connectionName]))
            throw new Exception('Perfil de conexión no establecido');
        
        $pro = $this->connectionsSettings[$connectionName];
        $params = array_values($pro);
        $charset = array_pop($params);
        if ($this->isSubQuery) {
            return;
        }
        if (empty($pro['host']) && empty($pro['socket'])) {
            throw new Exception('host de MySQL o socket no está establecido');
        }
        $mysqlic = new ReflectionClass('mysqli');
        $mysqli = $mysqlic->newInstanceArgs($params);
        if ($mysqli->connect_error) {
            throw new Exception('Error al Conectar ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error, $mysqli->connect_errno);
        }
        if (!empty($charset)) {
            $mysqli->set_charset($charset);
        }
        $this->_mysqli[$connectionName] = $mysqli;
    }


    public function disconnectAll()
    {
        foreach (array_keys($this->_mysqli) as $k) {
            $this->disconnect($k);
        }
    }

    /**
     * Establecer el nombre de conexión a utilizar en la siguiente consulta
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function connection($name)
    {
        if (!isset($this->connectionsSettings[$name]))
            throw new Exception('Conexión ' . $name . ' no fue agregado.');
        $this->defConnectionName = $name;
        return $this;
    }



    /**
     * Un método para desconectarse de la base de datos 
     *
     * @params string $connection nombre de conexión para desconectarse 
     * @throws Exception
     * @return void
     */
    public function disconnect($connection = 'default')
    {
        if (!isset($this->_mysqli[$connection]))
            return;
        $this->_mysqli[$connection]->close();
        unset($this->_mysqli[$connection]);
    }




    /**
     * Crear y almacenar en _mysqli nueva instancia mysqli
     * @param string $name
     * @param array $params
     * @return $this
     */
    public function addConnection($name, array $params)
    {
        $this->connectionsSettings[$name] = [];
        foreach (['host', 'username', 'password', 'db', 'port', 'socket', 'charset'] as $k) {
            $prm = isset($params[$k]) ? $params[$k] : null;
            if ($k == 'host') {
                if (is_object($prm))
                    $this->_mysqli[$name] = $prm;
                if (!is_string($prm))
                    $prm = null;
            }
            $this->connectionsSettings[$name][$k] = $prm;
        }
        return $this;
    }



    /**
     * Un método para obtener el objeto mysqli o crearlo en caso necesario
     * 
     * @return mysqli
     */
    public function mysqli()
    {
        if (!isset($this->_mysqli[$this->defConnectionName])) {
            $this->connect($this->defConnectionName);
        }
        return $this->_mysqli[$this->defConnectionName];
    }


    /**
     * Un método de devolver la instancia estática para permitir el acceso al 
  	 * objeto instanciado dentro de otra clase. 
 	 * Heredar esta clase requeriría recargar información de conexión. 
     *
     * @uses $db = Conexion::getInstance();
     *
     * @return Conexion Returns the current instance.
     */
    public static function getInstance()
    {
        return self::$_instance;
    }


    /**
     * Restablecer estados después de una ejecución 
     *
     * @return Conexion Returns the current instance.
     */
    protected function reset()
    {
        if ($this->traceEnabled) {
            $this->trace[] = array($this->_lastQuery, (microtime(true) - $this->traceStartQ), $this->_traceGetCaller());
        }
        $this->_where = array();
        $this->_having = array();
        $this->_join = array();
        $this->_joinAnd = array();
        $this->_orderBy = array();
        $this->_groupBy = array();
        $this->_bindParams = array(''); // Crear el índice 0 vacío 
        $this->_query = null;
        $this->_queryOptions = array();
        $this->returnType = 'array';
        $this->_nestJoin = false;
        $this->_forUpdate = false;
        $this->_lockInShareMode = false;
        $this->_tableName = '';
        $this->_lastInsertId = null;
        $this->_updateColumns = null;
        $this->_mapKey = null;
        $this->defConnectionName = 'default';
        return $this;
    }



    /**
     * Función auxiliar para crear dbObject con tipo de devolución JSON
     *
     * @return Conexion
     */
    public function jsonBuilder()
    {
        $this->returnType = 'json';
        return $this;
    }

    /**
     * Función de ayuda para crear dbObject con tipo de retorno de array 
 	 * Añadido para la coherencia con tipo de salida predeterminado 
     *
     * @return Conexion
     */
    public function arrayBuilder()
    {
        $this->returnType = 'array';
        return $this;
    }


    /**
     * Función de ayuda para crear dbObject con tipo de retorno de objeto. 
     *
     * @return Conexion
     */
    public function objectBuilder()
    {
        $this->returnType = 'object';
        return $this;
    }


    /**
     * Método para establecer un prefijo 
     *
     * @param string $prefix     Contiene un prefijo de tabla 
     * 
     * @return Conexion
     */
    public function setPrefix($prefix = '')
    {
        self::$prefix = $prefix;
        return $this;
    }


	/**
	 * Empuja una declaración no preparada a la pila mysqli. 
  	 * ADVERTENCIA: Utilizar con precaución. 
  	 * Este método no escapa de las cadenas por defecto, así que asegúrate de que nunca lo usarás en producción.
	 * 
	 * @param [[Type]] $query [[Description]]
	 */
	private function queryUnprepared($query)
	{	
		//  Ejecutar query
		$stmt = $this->mysqli()->query($query);
		// 	¿Falló? 
		if(!$stmt){
			throw new Exception("Unprepared Query Failed, ERRNO: ".$this->mysqli()->errno." (".$this->mysqli()->error.")", $this->mysqli()->errno);
		};
		
		// return stmt para uso futuro 
		return $stmt;
	}
	

	/**
     * Ejecutar consulta SQL sin procesar. 
     *
     * @param string $query      Consulta proporcionada por el usuario para ejecutar. 
     * @param array  $bindParams Variables array para enlazar con la instrucción SQL.
     *
     * @return array Contiene las filas devueltas de la consulta. 
     */
    public function rawQuery($query, $bindParams = null)
    {
        $params = array(''); // Create the empty 0 index
        $this->_query = $query;
        $stmt = $this->_prepareQuery();
        if (is_array($bindParams) === true) {
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->_determineType($val);
                array_push($params, $bindParams[$prop]);
            }
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
        }
        $stmt->execute();
        $this->count = $stmt->affected_rows;
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $params);
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }


    /**
     * Función auxiliar para ejecutar una consulta SQL sin procesar y devolver sólo 1 fila de resultados. 
  	 * Tenga en cuenta que la función no agrega 'límite 1' a la consulta por sí mismo 
 	 * La misma idea que getOne () 
     *
     * @param string $query      Consulta proporcionada por el usuario para ejecutar. 
     * @param array  $bindParams Variables array para enlazar con la instrucción SQL. 
     *
     * @return array|null Contiene la fila devuelta de la consulta.
     */
    public function rawQueryOne($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (is_array($res) && isset($res[0])) {
            return $res[0];
        }
        return null;
    }


    /**
     * Función de ayuda para ejecutar una consulta SQL sin formato y devolver sólo 1 columna de resultados. 
  	 * Si se encuentra 'límite 1', se devolverá la cadena en lugar del array 
	 * La misma idea que getValue () 
     *
     * @param string $query      Consulta proporcionada por el usuario para ejecutar. 
     * @param array  $bindParams Variables array para enlazar con la instrucción SQL. 
     *
     * @return mixed Contiene la fila devuelta de la consulta.
     */
    public function rawQueryValue($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (!$res) {
            return null;
        }
        $limit = preg_match('/limit\s+1;?$/i', $query);
        $key = key($res[0]);
        if (isset($res[0][$key]) && $limit == true) {
            return $res[0][$key];
        }
        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i][$key];
        }
        return $newRes;
    }


    /**
     * Un método para realizar la consulta SELECT 
     * 
     * @param string $query   Contiene una consulta de selección proporcionada por el usuario.
     * @param int|array $numRows Array para definir el límite de SQL en formato Array ($ count, $ offset) 
     *
     * @return array Contiene las filas devueltas de la consulta.
     */
    public function query($query, $numRows = null)
    {
        $this->_query = $query;
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }


    /**
     * Este método le permite especificar varias opciones (método de encadenamiento opcional) para las consultas SQL.
     *
     * @uses $Conexion->setQueryOption('name');
     *
     * @param string|array $options El nombre opcional de la consulta. 
     * 
     * @throws Exception
     * @return Conexion
     */
    public function setQueryOption($options)
    {
        $allowedOptions = Array('ALL', 'DISTINCT', 'DISTINCTROW', 'HIGH_PRIORITY', 'STRAIGHT_JOIN', 'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS',
            'LOW_PRIORITY', 'IGNORE', 'QUICK', 'MYSQLI_NESTJOIN', 'FOR UPDATE', 'LOCK IN SHARE MODE');
        if (!is_array($options)) {
            $options = Array($options);
        }
        foreach ($options as $option) {
            $option = strtoupper($option);
            if (!in_array($option, $allowedOptions)) {
                throw new Exception('Opción de consulta incorrecta: ' . $option);
            }
            if ($option == 'MYSQLI_NESTJOIN') {
                $this->_nestJoin = true;
            } elseif ($option == 'FOR UPDATE') {
                $this->_forUpdate = true;
            } elseif ($option == 'LOCK IN SHARE MODE') {
                $this->_lockInShareMode = true;
            } else {
                $this->_queryOptions[] = $option;
            }
        }
        return $this;
    }



    /**
     * Función para habilitar SQL_CALC_FOUND_ROWS en las consultas get 
     *
     * @return Conexion
     */
    public function withTotalCount()
    {
        $this->setQueryOption('SQL_CALC_FOUND_ROWS');
        return $this;
    }


    /**
     * Una conveniente función SELECT *. 
     *
     * @param string  $tableName El nombre de la tabla de base de datos con la que trabajar.
     * @param int|array $numRows Array para definir el límite de SQL en formato Array 
     * ($count, $offset) o sólo $count 
     * @param string $columns Columnas deseadas
     *
     * @return array Contiene las filas devueltas de la consulta select. 
     */
    public function get($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }
        $column = is_array($columns) ? implode(', ', $columns) : $columns;
        if (strpos($tableName, '.') === false) {
            $this->_tableName = self::$prefix . $tableName;
        } else {
            $this->_tableName = $tableName;
        }
        $this->_query = 'SELECT ' . implode(' ', $this->_queryOptions) . ' ' .
            $column . " FROM " . $this->_tableName;
        $stmt = $this->_buildQuery($numRows);
        if ($this->isSubQuery) {
            return $this;
        }
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();
        return $res;
    }


    /**
     * Una conveniente función SELECT * para obtener un registro. 
     *
     * @param string  $tableName El nombre de la tabla de base de datos con la que trabajar.
     * @param string  $columns Columnas deseadas 
     * 
     * @return array  Contiene las filas devueltas de la consulta select.
     */
    public function getOne($tableName, $columns = '*')
    {
        $res = $this->get($tableName, 1, $columns);
        if ($res instanceof Conexion) {
            return $res;
        } elseif (is_array($res) && isset($res[0])) {
            return $res[0];
        } elseif ($res) {
            return $res;
        }
        return null;
    }


    /**
     * Una conveniente función SELECT COLUMN para obtener un valor de una sola columna de una fila 
	 * 
	 * @param string $tableName El nombre de la tabla de base de datos con la que trabajar. 
	 * @param string $column La columna deseada 
	 * @param int $limit Límite de las filas para seleccionar.  
	 * Utilice null para unlimited..1 por defecto 
 	 * @return mixed Contiene el valor de una columna / array de valores devueltos 
     */
    public function getValue($tableName, $column, $limit = 1)
    {
        $res = $this->ArrayBuilder()->get($tableName, $limit, "{$column} AS retval");
        if (!$res) {
            return null;
        }
        if ($limit == 1) {
            if (isset($res[0]["retval"])) {
                return $res[0]["retval"];
            }
            return null;
        }
        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i]['retval'];
        }
        return $newRes;
    }


    /**
     * Insert método para agregar nueva fila
     *
     * @param string $tableName El nombre de la tabla.
     * @param array $insertData Datos que contienen información para insertar en el DB.
     *
     * @return bool Boolean indicando si la consulta insert se completó correctamente.
     */
    public function insert($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'INSERT');
    }


    /**
     * Insert método para agregar varias filas a la vez 
     *
     * @param string $tableName El nombre de la tabla.
     * @param array $multiInsertData Array de dos dimensiones de datos que contiene información para insertar en el DB.
     * @param array $dataKeys Opcional nombre de Tabla, si no se establece en insertDataSet. 
     *
     * @return bool|array Boolean indicando que la inserción falló (false), sino retorna id-array ([int])
     */
    public function insertMulti($tableName, array $multiInsertData, array $dataKeys = null)
    {
        // solo auto-entrega nuestras inserciones, si no se está ejecutando ninguna transacción
        $autoCommit = (isset($this->_transaction_in_progress) ? !$this->_transaction_in_progress : true);
        $ids = array();
        if($autoCommit) {
            $this->startTransaction();
        }
        foreach ($multiInsertData as $insertData) {
            if($dataKeys !== null) {
                // se aplican los nombres de las columnas, si no se supone que ya se han dado en los datos 
                $insertData = array_combine($dataKeys, $insertData);
            }
            $id = $this->insert($tableName, $insertData);
            if(!$id) {
                if($autoCommit) {
                    $this->rollback();
                }
                return false;
            }
            $ids[] = $id;
        }
        if($autoCommit) {
            $this->commit();
        }
        return $ids;
    }


    /**
     * Reemplazar método para agregar nueva fila 
     *
     * @param string $tableName El nombre de la tabla. 
	 * @param array $insertData Datos que contienen información para insertar en el DB. 
     *
     * @return bool Boolean indicando si el insert se completó correctamente. 
     */
    public function replace($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'REPLACE');
    }


    /**
     * Una función conveniente que devuelve TRUE si existe al menos un elemento que 
	 * satisface la condición donde especificó llamar al método "where" antes de éste. 
     *
     * @param string  $tableName El nombre de la tabla de base de datos con la que trabajar. 
     *
     * @return array Contiene las filas devueltas de la consulta select. 
     */
    public function has($tableName)
    {
        $this->getOne($tableName, '1');
        return $this->count >= 1;
    }


    /**
     * Update query. Asegúrese de llamar primero al método "where". 
     *
     * @param string $tableName El nombre de la tabla de base de datos con la que trabajar.
     * @param array  $tableData Array de datos para actualizar la fila deseada. 
     * @param int    $numRows   Limitar el número de filas que se pueden actualizar. 
     *
     * @return bool
     */
    public function update($tableName, $tableData, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }
        $this->_query = "UPDATE " . self::$prefix . $tableName;
        $stmt = $this->_buildQuery($numRows, $tableData);
        $status = $stmt->execute();
        $this->reset();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->count = $stmt->affected_rows;
        return $status;
    }


    /**
     * Delete query. Llame primero al método "where".
     *
     * @param string  $tableName El nombre de la tabla de base de datos con la que trabajar.
     * @param int|array $numRows Array para definir el límite de SQL en formato Array 
     * ($ count, $ offset) o sólo $count 
     *
     * @return bool Indica el éxito.  0 ó 1.
     */
    public function delete($tableName, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }
        $table = self::$prefix . $tableName;
        if (count($this->_join)) {
            $this->_query = "DELETE " . preg_replace('/.* (.*)/', '$1', $table) . " FROM " . $table;
        } else {
            $this->_query = "DELETE FROM " . $table;
        }
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->reset();
        return ($stmt->affected_rows > -1);	//	devuelve 0 si nada coincide con la instrucción where, o la actualización necesaria, -1 si el error 
    }


    /**
     * Este método le permite especificar múltiples (método de encadenamiento opcional)
     * instrucciones WHERE para las consultas SQL.
     *
     * @uses $Conexion->where('id', 7)->where('titulo', 'MiTitulo');
     *
     * @param string $whereProp  El nombre del campo de base de datos. 
     * @param mixed  $whereValue Valor del campo de base de datos. 
     * @param string $operator Operador de comparación.  El valor predeterminado es =
     * @param string $cond Condición de la sentencia where (OR, AND)
     *
     * @return Conexion
     */
    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        if (is_array($whereValue) && ($key = key($whereValue)) != "0") {
            $operator = $key;
            $whereValue = $whereValue[$key];
        }
        if (count($this->_where) == 0) {
            $cond = '';
        }
        $this->_where[] = array($cond, $whereProp, $operator, $whereValue);
        return $this;
    }


    /**
     * Esta función almacena el nombre de columna de actualización y el nombre de columna de la
     * Columna de autoincremento 
     *
     * @param array $updateColumns Variable con valores 
     * @param string $lastInsertId Valor de la variable 
     * 
     * @return Conexion
     */
    public function onDuplicate($updateColumns, $lastInsertId = null)
    {
        $this->_lastInsertId = $lastInsertId;
        $this->_updateColumns = $updateColumns;
        return $this;
    }


    /**
     *  Este método le permite especificar múltiples (método de encadenamiento opcional)
     *  instrucciones WHERE para consultas SQL.
     *
     * @uses $Conexion->orWhere('id', 7)->orWhere('title', 'MyTitle');
     *
     * @param string $whereProp  El nombre del campo de base de datos.
     * @param mixed  $whereValue Valor del campo de base de datos. 
     * @param string $operator Operador de comparación.  El valor predeterminado es = 
     *
     * @return Conexion
     */
    public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->where($whereProp, $whereValue, $operator, 'OR');
    }
    

    /**
     * Este método le permite especificar múltiples (método de encadenamiento opcional) 
     * instrucciones HAVING para las consultas SQL. 
     *
     * @uses $Conexion->having('SUM(tags) > 10')
     *
     * @param string $havingProp  El nombre del campo de base de datos.
     * @param mixed  $havingValue El valor del campo de base de datos. 
     * @param string $operator Operador de comparación.  El valor predeterminado es = 
     *
     * @return Conexion
     */
    public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        if (is_array($havingValue) && ($key = key($havingValue)) != "0") {
            $operator = $key;
            $havingValue = $havingValue[$key];
        }
        if (count($this->_having) == 0) {
            $cond = '';
        }
        $this->_having[] = array($cond, $havingProp, $operator, $havingValue);
        return $this;
    }


    /**
     * Este método le permite especificar múltiples (método de encadenamiento opcional) 
     * instrucciones HAVING para consultas SQL. 
     *
     * @uses $Conexion->orHaving('SUM(tags) > 10')
     *
     * @param string $havingProp  El nombre del campo de base de datos.
     * @param mixed  $havingValue El valor del campo de base de datos.
     * @param string $operator Operador de comparación.  El valor predeterminado es = 
     *
     * @return Conexion
     */
    public function orHaving($havingProp, $havingValue = null, $operator = null)
    {
        return $this->having($havingProp, $havingValue, $operator, 'OR');
    }


    /**
     * Este método le permite concatenar joins para la instrucción SQL final.
     *
     * @uses $Conexion->join('table1', 'field1 <> field2', 'LEFT')
     *
     * @param string $joinTable El nombre de la tabla. 
     * @param string $joinCondition La condicion.
     * @param string $joinType 'LEFT', 'INNER' etc.
     * 
     * @throws Exception
     * @return Conexion
     */
    public function join($joinTable, $joinCondition, $joinType = '')
    {
        $allowedTypes = array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER');
        $joinType = strtoupper(trim($joinType));
        if ($joinType && !in_array($joinType, $allowedTypes)) {
            throw new Exception('Wrong JOIN type: ' . $joinType);
        }
        if (!is_object($joinTable)) {
            $joinTable = self::$prefix . $joinTable;
        }
        $this->_join[] = Array($joinType, $joinTable, $joinCondition);
        return $this;
    }
	
	
	/**
	 * Este es un método básico que le permite importar datos crudos .CSV en una tabla 
	 * Consulte http://dev.mysql.com/doc/refman/5.7/es/load-data.html
	 * para obtener un archivo .csv válido.	 
	 * @param string $importTable        La tabla de base de datos en la que se importarán los datos.
	 * @param string $importFile         El archivo que se va a importar.  Utilice dos barras 
	 * diagonales inversas \\ 
	 * @param string $importSettings 	 Un Array que define la configuración de importación como se
	 * describe en el archivo README.md 
	 * @return boolean
	 */
	public function loadData($importTable, $importFile, $importSettings = null)
	{
		// Tenemos que comprobar si el archivo existe 
		if(!file_exists($importFile)) {
			// Lanzar una excepción
			throw new Exception("importCSV -> importFile ".$importFile." No existe!");
			return;
		}
		
		// Definir los valores por defecto 
 		// Lo fusionaremos más tarde 
		$settings 				= Array("fieldChar" => ';', "lineChar" => PHP_EOL, "linesToIgnore" => 1);
		
		// Comprobar los ajustes de importación  
		if(gettype($importSettings) == "array") {
			// Combina la matriz predeterminada con la personalizada
			$settings = array_merge($settings, $importSettings);
		}
	
		// Añade el prefijo a la tabla de importación
		$table = self::$prefix . $importTable;
		
		// Añadir 1 barra más a cada barra para que SQL lo interprete como un paso
		$importFile = str_replace("\\", "\\\\", $importFile);  
		
		// Generar sintaxis de SQL 
		$sqlSyntax = sprintf('LOAD DATA INFILE \'%s\' INTO TABLE %s', 
					$importFile, $table);
		
		// CAMPOS 
		$sqlSyntax .= sprintf(' FIELDS TERMINATED BY \'%s\'', $settings["fieldChar"]);
		if(isset($settings["fieldEnclosure"])) {
			$sqlSyntax .= sprintf(' ENCLOSED BY \'%s\'', $settings["fieldEnclosure"]);
		}
		
		// LINEAS
		$sqlSyntax .= sprintf(' LINES TERMINATED BY \'%s\'', $settings["lineChar"]);
		if(isset($settings["lineStarting"])) {
			$sqlSyntax .= sprintf(' STARTING BY \'%s\'', $settings["lineStarting"]);
		}
			
		// IGNORAR LINEAS
		$sqlSyntax .= sprintf(' IGNORE %d LINES', $settings["linesToIgnore"]);
	
		// Ejecute la consulta sin preparar porque LOAD DATA sólo funciona con instrucciones no preparadas.
		$result = $this->queryUnprepared($sqlSyntax);
		// ¿Hay filas modificadas? 
 		// Hágale saber al usuario si la importación ha fallado / ha tenido éxito 
		return (bool) $result;
	}
	

	/**
	 * Este método es útil para importar archivos XML en una tabla específica. 
	 * Compruebe la sintaxis de LOAD XML para su servidor MySQL. 	 
	 * @author Luis Candelario
	 * @param  string  $importTable    La tabla en la que se importarán los datos.
	 * @param  string  $importFile     El archivo que contiene los datos .XML. 
	 * @param  string  $importSettings Un Array que define la configuración de importación 
	 * como se describe en el archivo README.md
	 *                                                                                           
	 * @return boolean Devuelve true si la importación tuvo éxito, false si falló
	 */
	public function loadXml($importTable, $importFile, $importSettings = null)
	{
		// Tenemos que comprobar si el archivo existe 
		if(!file_exists($importFile)) {
			// No existe 
			throw new Exception("loadXml:  El archivo de importación no existe");
			return;
		}
		
		// Crear valores predeterminados
		$settings 			= Array("linesToIgnore" => 0);
		// Comprobar los ajustes de importación 
		if(gettype($importSettings) == "array") {
			$settings = array_merge($settings, $importSettings);
		}
		// Añade el prefijo a la tabla de importación 
		$table = self::$prefix . $importTable;
		
		// Añadir 1 barra más a cada barra para que SQL lo interprete como un paso
		$importFile = str_replace("\\", "\\\\", $importFile);  
		
		// Generar sintaxis de SQL 
		$sqlSyntax = sprintf('LOAD XML INFILE \'%s\' INTO TABLE %s', 
								 $importFile, $table);
		
		// CAMPOS 
		if(isset($settings["rowTag"])) {
			$sqlSyntax .= sprintf(' ROWS IDENTIFIED BY \'%s\'', $settings["rowTag"]);
		}
			
		// IGNORAR LINEAS
		$sqlSyntax .= sprintf(' IGNORE %d LINES', $settings["linesToIgnore"]);
		
		// Ejecute la consulta sin preparar porque LOAD XML sólo funciona con sentencias no preparadas.
		$result = $this->queryUnprepared($sqlSyntax);
		// ¿Hay filas modificadas? 
 		// Hágale saber al usuario si la importación ha fallado / ha tenido éxito 
		return (bool) $result;
	}


    /**
     * Este método le permite especificar múltiples instrucciones ORDER BY (método de encadenamiento opcional) para consultas SQL.
     *
     * @uses $Conexion->orderBy('id', 'desc')->orderBy('name', 'desc', '^[a-z]')->orderBy('name', 'desc');
     *
     * @param string $orderByField El nombre del campo de la base de datos.
     * @param string $orderByDirection Dirección de orden.
     * @param mixed $customFieldsOrRegExp Array con fieldset para ORDER BY FIELD () ordenando 
     * o cadena con expresión regular para ORDER BY REGEX ordenando
     * 
     * @throws Exception
     * @return Conexion
     */
    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFieldsOrRegExp = null)
    {
        $allowedDirection = Array("ASC", "DESC");
        $orderbyDirection = strtoupper(trim($orderbyDirection));
        $orderByField = preg_replace("/[^ -a-z0-9\.\(\),_`\*\'\"]+/i", '', $orderByField);
        // Añade prefijo de tabla a orderByField si es necesario. 
  		// FIXME: Estamos agregando prefijo sólo si la tabla está incluida en `` para distinguir alias de nombres de tabla 
        $orderByField = preg_replace('/(\`)([`a-zA-Z0-9_]*\.)/', '\1' . self::$prefix . '\2', $orderByField);
        if (empty($orderbyDirection) || !in_array($orderbyDirection, $allowedDirection)) {
            throw new Exception('Dirección de orden equivocada: ' . $orderbyDirection);
        }
        if (is_array($customFieldsOrRegExp)) {
            foreach ($customFieldsOrRegExp as $key => $value) {
                $customFieldsOrRegExp[$key] = preg_replace("/[^-a-z0-9\.\(\),_` ]+/i", '', $value);
            }
            $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFieldsOrRegExp) . '")';
        }elseif(is_string($customFieldsOrRegExp)){
	    $orderByField = $orderByField . " REGEXP '" . $customFieldsOrRegExp . "'";
	}elseif($customFieldsOrRegExp !== null){
	    throw new Exception('Campo personalizado incorrecto o Expresion Regular: ' . $customFieldsOrRegExp);
	}
        $this->_orderBy[$orderByField] = $orderbyDirection;
        return $this;
    }


    /**
     * Este método le permite especificar instrucciones GROUP BY múltiples (método de encadenamiento opcional) para consultas SQL.
     *
     * @uses $Conexion->groupBy('name');
     *
     * @param string $groupByField El nombre del campo de la base de datos.
     *
     * @return Conexion
     */
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);
        $this->_groupBy[] = $groupByField;
        return $this;
    }
	
	
	/**
	 * Este método establece el método de bloqueo de tabla actual. 
	 * 
	 * @param  string   $method El método de bloqueo de tabla.  Puede ser READ o WRITE. 
	 *                                                                 
	 * @throws Exception
	 * @return Conexion
	 */
	public function setLockMethod($method)
	{
		// Cambiar la cadena mayúscula
		switch(strtoupper($method)) {
			// Es READ o WRITE?
			case "READ" || "WRITE":
				// Correcto
				$this->_tableLockMethod = $method;
				break;
			default:
				// Si no lanza una excepción 
				throw new Exception("Tipo de bloqueo incorrecto: Puede ser READ o WRITE");
				break;
		}
		return $this;
	}
	

	/**
	 * Bloquea una tabla para la acción R / W. 
	 * 
	 * @param string  $table La Tabla a bloquearse. Puede ser una tabla o una vista. 
	 *                       
	 * @throws Exception
	 * @return Conexion si es correcto;
	 */
	public function lock($table)
	{
		// Query Principal
		$this->_query = "LOCK TABLES";
		
		// Es la tabla un array?
		if(gettype($table) == "array") {
			// Loop a través de él y adjuntarlo a la consulta
			foreach($table as $key => $value) {
				if(gettype($value) == "string") {
					if($key > 0) {
						$this->_query .= ",";
					}
					$this->_query .= " ".self::$prefix.$value." ".$this->_tableLockMethod;
				}
			}
		}
		else{
			// Construir el prefijo de la tabla 
			$table = self::$prefix . $table;
			
			// Construir la query
			$this->_query = "LOCK TABLES ".$table." ".$this->_tableLockMethod;
		}
		// Ejecute la consulta sin preparar porque LOCK sólo funciona con sentencias no preparadas.
		$result = $this->queryUnprepared($this->_query);
        $errno  = $this->mysqli()->errno;
			
		// Resetea la query
		$this->reset();
		// Hay filas modificadas?
		if($result) {	
			// Devuelve true
			// No podemos devolvernos a nosotros mismos porque si una tabla se bloquea, todas los demás se desbloquean!
			return true;
		}
		// Algo salió mal
		else {
			throw new Exception("Bloqueo de la tabla ".$table." fallido", $errno);
		}
		// Devuelve el valor de éxito
		return false;
	}
	

	/**
	 * Desbloquea todas las tablas de una base de datos. 
	 * También realiza transacciones.
	 * 
	 * @return Conexion
	 */
	public function unlock()
	{
		// Construir la query
		$this->_query = "UNLOCK TABLES";
		// Ejecute la consulta sin preparar porque UNLOCK y LOCK sólo funcionan con instrucciones no preparadas.
		$result = $this->queryUnprepared($this->_query);
        $errno  = $this->mysqli()->errno;
		// Restablecer la query
		$this->reset();
		// ¿Hay filas modificadas?
		if($result) {
			// Regresa a ti mismo
			return $this;
		}
		// Algo salió mal
		else {
			throw new Exception("Error al desbloquear las tablas", $errno);
		}
		
	
		// Regresa a ti mismo
		return $this;
	}
	

    /**
     * Este método devuelve el identificador del último elemento insertado
     *
     * @return int El último ID de artículo insertado.
     */
    public function getInsertId()
    {
        return $this->mysqli()->insert_id;
    }


    /**
     * Evite los caracteres dañinos que puedan afectar a una consulta.
     *
     * @param string $str El string a escapar.
     *
     * @return string El string escapado.
     */
    public function escape($str)
    {
        return $this->mysqli()->real_escape_string($str);
    }


    /**
     * Método para llamar a mysqli-> ping () para mantener las conexiones inactivas abiertas 
     * en los scripts de larga ejecución, o para volver a conectar las conexiones temporizadas
     * (si php.ini tiene el mysqli.reconnect global configurado como true). No se puede hacer 
     * esto directamente usando el objeto ya que _mysqli está protegido.
     *
     * @return bool Verdadero si la conexión ha terminado
     */
    public function ping()
    {
        return $this->mysqli()->ping();
    }


    /**
     * Este método es necesario para las declaraciones preparadas. Ellos requieren
     * El tipo de datos del campo a enlazar con "i" s, etc.
     * Esta función toma la entrada, determina qué tipo es,
     * Y luego actualiza el param_type.
     *
     * @param mixed $item Entrada para determinar el tipo.
     * @return string Los tipos de parámetro unidos.
     */
    protected function _determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }


    /**
     * Función de ayuda para agregar variables en el array de parámetros de enlace
     *
     * @param string Valor de Variable
     */
    protected function _bindParam($value)
    {
        $this->_bindParams[0] .= $this->_determineType($value);
        array_push($this->_bindParams, $value);
    }


    /**
     * Función auxiliar para agregar variables en el array de parámetros de enlace a granel
     *
     * @param array $values Variable con valores
     */
    protected function _bindParams($values)
    {
        foreach ($values as $value) {
            $this->_bindParam($value);
        }
    }


    /**
     * Función auxiliar para agregar variables en el array de parámetros de enlace y devolverá 
     * su parte SQL de la consulta de acuerdo al operador en '$operator?' O '$operator ($subquery)'
     *
     * @param string $operator
     * @param mixed $value Variable con valores
     * 
     * @return string
     */
    protected function _buildPair($operator, $value)
    {
        if (!is_object($value)) {
            $this->_bindParam($value);
            return ' ' . $operator . ' ? ';
        }
        $subQuery = $value->getSubQuery();
        $this->_bindParams($subQuery['params']);
        return " " . $operator . " (" . $subQuery['query'] . ") " . $subQuery['alias'];
    }


    /**
     * Función interna para crear y ejecutar llamadas INSERT / REPLACE
     *
     * @param string $tableName El nombre de la tabla.
     * @param array $insertData Datos que contienen información para insertar en la DB.
     * @param string $operation Tipo de operacion (INSERT, REPLACE)
     *
     * @return bool Boolean Indicando si la consulta insert se completó correctamente.
     */
    private function _buildInsert($tableName, $insertData, $operation)
    {
        if ($this->isSubQuery) {
            return;
        }
        $this->_query = $operation . " " . implode(' ', $this->_queryOptions) . " INTO " . self::$prefix . $tableName;
        $stmt = $this->_buildQuery(null, $insertData);
        $status = $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $haveOnDuplicate = !empty ($this->_updateColumns);
        $this->reset();
        $this->count = $stmt->affected_rows;
        if ($stmt->affected_rows < 1) {
            // En caso de uso de onDuplicate (), si no se han insertado filas
            if ($status && $haveOnDuplicate) {
                return true;
            }
            return false;
        }
        if ($stmt->insert_id > 0) {
            return $stmt->insert_id;
        }
        return true;
    }


    /**
     * Método de abstracción que compilará la instrucción WHERE,
     * Los datos de actualización pasados y las filas deseadas.
     * A continuación, crea la consulta SQL.
     *
     * @param int|array $numRows Array para definir el límite de SQL en formato Array 
     * ($count, $offset) o sólo $count
     * @param array $tableData Debe contener un array de datos para actualizar la base de datos.
     *
     * @return mysqli_stmt Devuelve el objeto $stmt.
     */
    protected function _buildQuery($numRows = null, $tableData = null)
    {
        $this->_buildJoin();
        $this->_buildInsertQuery($tableData);
        $this->_buildCondition('WHERE', $this->_where);
        $this->_buildGroupBy();
        $this->_buildCondition('HAVING', $this->_having);
        $this->_buildOrderBy();
        $this->_buildLimit($numRows);
        $this->_buildOnDuplicate($tableData);
        
        if ($this->_forUpdate) {
            $this->_query .= ' FOR UPDATE';
        }
        if ($this->_lockInShareMode) {
            $this->_query .= ' LOCK IN SHARE MODE';
        }
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $this->_bindParams);
        if ($this->isSubQuery) {
            return;
        }
        // Prepare query
        $stmt = $this->_prepareQuery();
        // Bind parameters to statement if any
        if (count($this->_bindParams) > 1) {
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($this->_bindParams));
        }
        return $stmt;
    }


    /**
     * Este método auxiliar se encarga de las sentencias preparadas '"método bind_result
     * Cuando el número de variables a pasar es desconocido.
     *
     * @param mysqli_stmt $stmt Igual al objeto de sentencia preparado.
     *
     * @return array Los resultados de la búsqueda de SQL.
     */
    protected function _dynamicBindResults(mysqli_stmt $stmt)
    {
        $parameters = array();
        $results = array();
        /**
         * @see http://php.net/manual/en/mysqli-result.fetch-fields.php
         */
        $mysqlLongType = 252;
        $shouldStoreResult = false;
        $meta = $stmt->result_metadata();
        // Si $ meta es false pero sqlstate es true, no hay error sql pero la consulta es
        // lo más probable un update / insert / delete que no produce ningún resultado
        if (!$meta && $stmt->sqlstate)
            return array();
        $row = array();
        while ($field = $meta->fetch_field()) {
            if ($field->type == $mysqlLongType) {
                $shouldStoreResult = true;
            }
            if ($this->_nestJoin && $field->table != $this->_tableName) {
                $field->table = substr($field->table, strlen(self::$prefix));
                $row[$field->table][$field->name] = null;
                $parameters[] = & $row[$field->table][$field->name];
            } else {
                $row[$field->name] = null;
                $parameters[] = & $row[$field->name];
            }
        }
        // Evitar errores de memoria en php 5.2 y 5.3. Mysqli asigna mucha memoria por mucho tiempo
        // Así que para evitar problemas de memoria store_result se utiliza
        if ($shouldStoreResult) {
            $stmt->store_result();
        }
        call_user_func_array(array($stmt, 'bind_result'), $parameters);
        $this->totalCount = 0;
        $this->count = 0;
        while ($stmt->fetch()) {
            if ($this->returnType == 'object') {
                $result = new stdClass ();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        $result->$key = new stdClass ();
                        foreach ($val as $k => $v) {
                            $result->$key->$k = $v;
                        }
                    } else {
                        $result->$key = $val;
                    }
                }
            } else {
                $result = array();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            $result[$key][$k] = $v;
                        }
                    } else {
                        $result[$key] = $val;
                    }
                }
            }
            $this->count++;
            if ($this->_mapKey) {
                $results[$row[$this->_mapKey]] = count($row) > 2 ? $result : end($result);
            } else {
                array_push($results, $result);
            }
        }
        if ($shouldStoreResult) {
            $stmt->free_result();
        }
        $stmt->close();
        // Los procedimientos almacenados a veces pueden devolver más de 1 resultado
        if ($this->mysqli()->more_results()) {
            $this->mysqli()->next_result();
        }
        if (in_array('SQL_CALC_FOUND_ROWS', $this->_queryOptions)) {
            $stmt = $this->mysqli()->query('SELECT FOUND_ROWS()');
            $totalCount = $stmt->fetch_row();
            $this->totalCount = $totalCount[0];
        }
        if ($this->returnType == 'json') {
            return json_encode($results);
        }
        return $results;
    }


    /**
     * Insert/Update Auxiliar de consulta
     * 
     * @param array $tableData
     * @param array $tableColumns
     * @param bool $isInsert INSERT Bandera de operación
     * 
     * @throws Exception
     */
    public function _buildDataPairs($tableData, $tableColumns, $isInsert)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];
            if (!$isInsert) {
                if(strpos($column,'.')===false) {
                    $this->_query .= "`" . $column . "` = ";
                } else {
                    $this->_query .= str_replace('.','.`',$column) . "` = ";
                }
            }
            // Subquery valor
            if ($value instanceof Conexion) {
                $this->_query .= $this->_buildPair("", $value) . ", ";
                continue;
            }
            // Valor simple
            if (!is_array($value)) {
                $this->_bindParam($value);
                $this->_query .= '?, ';
                continue;
            }
            // Valor de la función 
            $key = key($value);
            $val = $value[$key];
            switch ($key) {
                case '[I]':
                    $this->_query .= $column . $val . ", ";
                    break;
                case '[F]':
                    $this->_query .= $val[0] . ", ";
                    if (!empty($val[1])) {
                        $this->_bindParams($val[1]);
                    }
                    break;
                case '[N]':
                    if ($val == null) {
                        $this->_query .= "!" . $column . ", ";
                    } else {
                        $this->_query .= "!" . $val . ", ";
                    }
                    break;
                default:
                    throw new Exception("Funcionamiento incorrecto");
            }
        }
        $this->_query = rtrim($this->_query, ', ');
    }


    /**
     * Función auxiliar para agregar variables a la instrucción de consulta
     *
     * @param array $tableData Variable con valores
     */
    protected function _buildOnDuplicate($tableData)
    {
        if (is_array($this->_updateColumns) && !empty($this->_updateColumns)) {
            $this->_query .= " ON DUPLICATE KEY UPDATE ";
            if ($this->_lastInsertId) {
                $this->_query .= $this->_lastInsertId . "=LAST_INSERT_ID (" . $this->_lastInsertId . "), ";
            }
            foreach ($this->_updateColumns as $key => $val) {
                // Omitir todos los parámetros sin valor
                if (is_numeric($key)) {
                    $this->_updateColumns[$val] = '';
                    unset($this->_updateColumns[$key]);
                } else {
                    $tableData[$key] = $val;
                }
            }
            $this->_buildDataPairs($tableData, array_keys($this->_updateColumns), false);
        }
    }


    /**
     * Método de abstracción que construirá una parte INSERT o UPDATE de la consulta
     * 
     * @param array $tableData
     */
    protected function _buildInsertQuery($tableData)
    {
        if (!is_array($tableData)) {
            return;
        }
        $isInsert = preg_match('/^[INSERT|REPLACE]/', $this->_query);
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            if (isset ($dataColumns[0]))
                $this->_query .= ' (`' . implode($dataColumns, '`, `') . '`) ';
            $this->_query .= ' VALUES (';
        } else {
            $this->_query .= " SET ";
        }
        $this->_buildDataPairs($tableData, $dataColumns, $isInsert);
        if ($isInsert) {
            $this->_query .= ')';
        }
    }


    /**
     * Método de abstracción que construirá la parte de las condiciones WHERE
     * 
     * @param string $operator
     * @param array $conditions
     */
    protected function _buildCondition($operator, &$conditions)
    {
        if (empty($conditions)) {
            return;
        }
        //Preparar la parte de la consulta WHERE
        $this->_query .= ' ' . $operator;
        foreach ($conditions as $cond) {
            list ($concat, $varName, $operator, $val) = $cond;
            $this->_query .= " " . $concat . " " . $varName;
            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $operator . ' (';
                    if (is_object($val)) {
                        $comparison .= $this->_buildPair("", $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->_query .= " $operator ? AND ? ";
                    $this->_bindParams($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->_query.= $operator . $this->_buildPair("", $val);
                    break;
                default:
                    if (is_array($val)) {
                        $this->_bindParams($val);
                    } elseif ($val === null) {
                        $this->_query .= ' ' . $operator . " NULL";
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->_query .= $this->_buildPair($operator, $val);
                    }
            }
        }
    }


    /**
     * Método de abstracción que construirá la parte GROUP BY de la sentencia WHERE
     *
     * @return void
     */
    protected function _buildGroupBy()
    {
        if (empty($this->_groupBy)) {
            return;
        }
        $this->_query .= " GROUP BY ";
        foreach ($this->_groupBy as $key => $value) {
            $this->_query .= $value . ", ";
        }
        $this->_query = rtrim($this->_query, ', ') . " ";
    }


    /**
     * Método de abstracción que construirá la parte ORDER BY de la sentencia WHERE
     *
     * @return void
     */
    protected function _buildOrderBy()
    {
        if (empty($this->_orderBy)) {
            return;
        }
        $this->_query .= " ORDER BY ";
        foreach ($this->_orderBy as $prop => $value) {
            if (strtolower(str_replace(" ", "", $prop)) == 'rand()') {
                $this->_query .= "rand(), ";
            } else {
                $this->_query .= $prop . " " . $value . ", ";
            }
        }
        $this->_query = rtrim($this->_query, ', ') . " ";
    }


    /**
     * Método de abstracción que construirá la parte LIMIT de la sentencia WHERE
     *
     * @param int|array $numRows Array para definir el límite de SQL en formato Array 
     * ($ count, $ offset) O sólo $count
     * 
     * @return void
     */
    protected function _buildLimit($numRows)
    {
        if (!isset($numRows)) {
            return;
        }
        if (is_array($numRows)) {
            $this->_query .= ' LIMIT ' . (int) $numRows[0] . ', ' . (int) $numRows[1];
        } else {
            $this->_query .= ' LIMIT ' . (int) $numRows;
        }
    }


    /**
     * El método intenta preparar la consulta SQL
     * Y arroja un error si hubo un problema.
     *
     * @return mysqli_stmt
     * @throws Exception
     */
    protected function _prepareQuery()
    {
        if (!$stmt = $this->mysqli()->prepare($this->_query)) {
            $msg = $this->mysqli()->error . " query: " . $this->_query;
            $num = $this->mysqli()->errno;
            $this->reset();
            throw new Exception($msg, $num);
        }
        if ($this->traceEnabled) {
            $this->traceStartQ = microtime(true);
        }
        return $stmt;
    }


    /**
     * El array de datos referenciados es requerida por mysqli desde PHP 5.3+
     * 
     * @param array $arr
     *
     * @return array
     */
    protected function refValues(array &$arr)
    {
        //Se requiere referencia en los argumentos de la función para que HHVM funcione
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = & $arr[$key];
            }
            return $refs;
        }
        return $arr;
    }


    /**
     * Función para reemplazar ? Con variables de variable de enlace
     * 
     * @param string $str
     * @param array $vals
     *
     * @return string
     */
    protected function replacePlaceHolders($str, $vals)
    {
        $i = 1;
        $newStr = "";
        if (empty($vals)) {
            return $str;
        }
        while ($pos = strpos($str, "?")) {
            $val = $vals[$i++];
            if (is_object($val)) {
                $val = '[object]';
            }
            if ($val === null) {
                $val = 'NULL';
            }
            $newStr .= substr($str, 0, $pos) . "'" . $val . "'";
            $str = substr($str, $pos + 1);
        }
        $newStr .= $str;
        return $newStr;
    }


    /**
     * El método devuelve la última consulta ejecutada
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->_lastQuery;
    }


    /**
     * Método devuelve error mysqli
     *
     * @return string
     */
    public function getLastError()
    {
        if (!isset($this->_mysqli[$this->defConnectionName])) {
            return "mysqli is null";
        }
        return trim($this->_stmtError . " " . $this->mysqli()->error);
    }

    /**
     * Método devuelve código de error mysql
     * @return int
     */
    public function getLastErrno () {
        return $this->_stmtErrno;
    }


    /**
     * Principalmente método interno para obtener la consulta y sus parámetros 
     * fuera del objeto de subconsulta después de get () y getAll ()
     *
     * @return array
     */
    public function getSubQuery()
    {
        if (!$this->isSubQuery) {
            return null;
        }
        array_shift($this->_bindParams);
        $val = Array('query' => $this->_query,
            'params' => $this->_bindParams,
            'alias' => isset($this->connectionsSettings[$this->defConnectionName]) ? $this->connectionsSettings[$this->defConnectionName]['host'] : null
        );
        $this->reset();
        return $val;
    }
        

    /* Funciones auxiliares */
    /**
     * Método devuelve la función de intervalo generada como una cadena
     *
     * @param string $diff Intervalo en los formatos:
     *        "1", "-1d" o "- 1 día" - Por intervalo - 1 día
     * Intervalos soportados [s]egundos, [m]inutos, [h]hora, [d] día, [M]es, [Y]ear
     * Predeterminado null;
     * @param string $func Fecha inicial
     *
     * @return string
     */
    public function interval($diff, $func = "NOW()")
    {
        $types = Array("s" => "second", "m" => "minute", "h" => "hour", "d" => "day", "M" => "month", "Y" => "year");
        $incr = '+';
        $items = '';
        $type = 'd';
        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }
            if (!empty($matches[2])) {
                $items = $matches[2];
            }
            if (!empty($matches[3])) {
                $type = $matches[3];
            }
            if (!in_array($type, array_keys($types))) {
                throw new Exception("Tipo de intervalo no válido'{$diff}'");
            }
            $func .= " " . $incr . " interval " . $items . " " . $types[$type] . " ";
        }
        return $func;
    }


    /**
     * Método devuelve la función de intervalo generada como una función de insert / update
     *
     * @param string $diff Intervalo en los formatos:
     *        "1", "-1d" o "- 1 día" - Por intervalo - 1 día
     * Intervalos soportados [s]egundos, [m]inutos, [h]hora, [d] día, [M]es, [Y]ear
     * Predeterminado null;
     * @param string $func Fecha inicial
     *
     * @return array
     */
    public function now($diff = null, $func = "NOW()")
    {
        return array("[F]" => Array($this->interval($diff, $func)));
    }


    /**
     * Método genera llamada de función incremental
     * 
     * @param int $num Incremento por int o float. 1 por defecto
     * 
     * @throws Exception
     * @return array
     */
    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('El argumento suministrado a inc debe ser un número');
        }
        return array("[I]" => "+" . $num);
    }


    /**
     * El método genera llamada de función decrimental
     * 
     * @param int $num Decrementoa por int o float. 1 por defecto
     * 
     * @return array
     */
    public function dec($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('El argumento suministrado a dec debe ser un número');
        }
        return array("[I]" => "-" . $num);
    }


    /**
     * El método genera una llamada de función booleana de cambio
     * 
     * @param string $col Nombre de la columna. Null por defecto
     * 
     * @return array
     */
    public function not($col = null)
    {
        return array("[N]" => (string) $col);
    }


    /**
     * Método genera llamada de función definida por el usuario
     * 
     * @param string $expr Cuerpo de la función del usuario
     * @param array $bindParams
     * 
     * @return array
     */
    public function func($expr, $bindParams = null)
    {
        return array("[F]" => array($expr, $bindParams));
    }


    /**
     * El método crea un nuevo objeto Conexion para una generación de subconsulta
     * 
     * @param string $subQueryAlias
     * 
     * @return Conexion
     */
    public static function subQuery($subQueryAlias = "")
    {
        return new self(array('host' => $subQueryAlias, 'isSubQuery' => true));
    }


    /**
     * Método devuelve una copia de un objeto de subconsulta Conexion
     *
     * @return Conexion Nuevo objeto Conexion
     */
    public function copy()
    {
        $copy = unserialize(serialize($this));
        $copy->_mysqli = [];
        return $copy;
    }


    /**
     * Iniciar una transacción
     *
     * @uses mysqli->autocommit(false)
     * @uses register_shutdown_function(array($this, "_transaction_shutdown_check"))
     */
    public function startTransaction()
    {
        $this->mysqli()->autocommit(false);
        $this->_transaction_in_progress = true;
        register_shutdown_function(array($this, "_transaction_status_check"));
    }


    /**
     * Hacer Transaccion
     *
     * @uses mysqli->commit();
     * @uses mysqli->autocommit(true);
     */
    public function commit()
    {
        $result = $this->mysqli()->commit();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }


    /**
     * Función de reversión de transacciones
     *
     * @uses mysqli->rollback();
     * @uses mysqli->autocommit(true);
     */
    public function rollback()
    {
        $result = $this->mysqli()->rollback();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }


    /**
     * Controlador de apagado para revertir las operaciones no confirmadas para mantener 
     * las operaciones atómicas sanas.
     * @uses mysqli->rollback();
     */
    public function _transaction_status_check()
    {
        if (!$this->_transaction_in_progress) {
            return;
        }
        $this->rollback();
    }


    /**
     * Interruptor de seguimiento del tiempo de ejecución de la consulta
     *
     * @param bool $enabled Habilitar el seguimiento del tiempo de ejecución
     * @param string $stripPrefix Prefijo de la ruta de acceso en el registro de ejecuciones
     * 
     * @return Conexion
     */
    public function setTrace($enabled, $stripPrefix = null)
    {
        $this->traceEnabled = $enabled;
        $this->traceStripPrefix = $stripPrefix;
        return $this;
    }


    /**
     * Obtener dónde y qué función se llamó para la consulta almacenada en Conexion-> trace
     *
     * @return string Con información
     */
    private function _traceGetCaller()
    {
        $dd = debug_backtrace();
        $caller = next($dd);
        while (isset($caller) && $caller["file"] == __FILE__) {
            $caller = next($dd);
        }
        return __CLASS__ . "->" . $caller["function"] . "() >>  file \"" .
            str_replace($this->traceStripPrefix, '', $caller["file"]) . "\" line #" . $caller["line"] . " ";
    }


    /**
     * Método para comprobar si la tabla necesaria se crea
     *
     * @param array $tables Nombre de tabla o una matriz de nombres de tabla para comprobar
     *
     * @return bool True si existe una tabla
     */
    public function tableExists($tables)
    {
        $tables = !is_array($tables) ? Array($tables) : $tables;
        $count = count($tables);
        if ($count == 0) {
            return false;
        }
        foreach ($tables as $i => $value)
            $tables[$i] = self::$prefix . $value;
        $db = isset($this->connectionsSettings[$this->defConnectionName]) ? $this->connectionsSettings[$this->defConnectionName]['db'] : null;
        $this->where('table_schema', $db);
        $this->where('table_name', $tables, 'in');
        $this->get('information_schema.tables', $count);
        return $this->count == $count;
    }


    /**
     * Devuelve el resultado como una matriz asociativa con valor de campo $idField 
     * utilizado como clave de registro
     * 
     * Array Devuelve un array ($k => $v) if get (.. "param1, param2"), array ($k => array ($v, $v) de otra manera
     * 
     * @param string $idField Nombre de campo a utilizar para una clave de elemento asignada
     *
     * @return Conexion
     */
    public function map($idField)
    {
        $this->_mapKey = $idField;
        return $this;
    }


    /**
     * Envoltorio de paginación para obtener ()
     *
     * @access public
     * @param string  $table El nombre de la tabla de base de datos para trabajar
     * @param int $page Numero de Pagina
     * @param array|string $fields Array o una lista de campos separados por comas para buscar
     * @return array
     */
    public function paginate ($table, $page, $fields = null) {
        $offset = $this->pageLimit * ($page - 1);
        $res = $this->withTotalCount()->get ($table, Array ($offset, $this->pageLimit), $fields);
        $this->totalPages = ceil($this->totalCount / $this->pageLimit);
        return $res;
    }


    /**
     * Este método le permite especificar múltiples (método de encadenamiento opcional) 
     * WHERE para los join en la tabla de la consulta SQL.
     *
     * @uses $dbWrapper->joinWhere('user u', 'u.id', 7)->where('user u', 'u.title', 'MyTitle');
     *
     * @param string $whereJoin  El nombre de la tabla seguido de su prefijo.
     * @param string $whereProp  El nombre del campo de la base de datos.
     * @param mixed  $whereValue El valor del campo de base de datos.
     *
     * @return dbWrapper
     */
    public function joinWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->_joinAnd[$whereJoin][] = Array ($cond, $whereProp, $operator, $whereValue);
        return $this;
    }


    /**
     * Este método permite especificar instrucciones multiple (método de encadenamiento opcional)
     * WHERE para los join en la tabla de la consulta SQL.
     *
     * @uses $dbWrapper->joinWhere('user u', 'u.id', 7)->where('user u', 'u.title', 'MyTitle');
     *
     * @param string $whereJoin  El nombre de la tabla seguido de su prefijo.
     * @param string $whereProp  El nombre del campo de la base de datos.
     * @param mixed  $whereValue El valor del campo de base de datos.
     *
     * @return dbWrapper
     */
    public function joinOrWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        return $this->joinWhere($whereJoin, $whereProp, $whereValue, $operator, 'OR');
    }


    /**
     * Método de abstracción que construirá una parte JOIN de la consulta
     */
    protected function _buildJoin () {
        if (empty ($this->_join))
            return;
        foreach ($this->_join as $data) {
            list ($joinType,  $joinTable, $joinCondition) = $data;
            if (is_object ($joinTable))
                $joinStr = $this->_buildPair ("", $joinTable);
            else
                $joinStr = $joinTable;
            $this->_query .= " " . $joinType. " JOIN " . $joinStr ." on " . $joinCondition;
            // Add join and query
            if (!empty($this->_joinAnd) && isset($this->_joinAnd[$joinStr])) {
                foreach($this->_joinAnd[$joinStr] as $join_and_cond) {
                    list ($concat, $varName, $operator, $val) = $join_and_cond;
                    $this->_query .= " " . $concat ." " . $varName;
                    $this->conditionToSql($operator, $val);
                }
            }
        }
    }


    /**
     * Convertir una condición y un valor en la cadena sql
     * @param  String $operator El operador de restricción where
     * @param  String $val    El valor de restricción where
     */
    private function conditionToSql($operator, $val) {
        switch (strtolower ($operator)) {
            case 'not in':
            case 'in':
                $comparison = ' ' . $operator. ' (';
                if (is_object ($val)) {
                    $comparison .= $this->_buildPair ("", $val);
                } else {
                    foreach ($val as $v) {
                        $comparison .= ' ?,';
                        $this->_bindParam ($v);
                    }
                }
                $this->_query .= rtrim($comparison, ',').' ) ';
                break;
            case 'not between':
            case 'between':
                $this->_query .= " $operator ? AND ? ";
                $this->_bindParams ($val);
                break;
            case 'not exists':
            case 'exists':
                $this->_query.= $operator . $this->_buildPair ("", $val);
                break;
            default:
                if (is_array ($val))
                    $this->_bindParams ($val);
                else if ($val === null)
                    $this->_query .= $operator . " NULL";
                else if ($val != 'DBNULL' || $val == '0')
                    $this->_query .= $this->_buildPair ($operator, $val);
        }
    }
}

?>