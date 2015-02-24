<?php
require_once("Rest.inc.php");

class API extends REST 
{
	public $data = "";
	const DB_SERVER = "localhost";
	const DB_USER = "networ_user";
	const DB_PASSWORD = "admin.2014";
	const DB = "networ_db";
	
	private $db = NULL;

public function __construct()
{
	parent::__construct();// Init parent contructor
	$this->dbConnect();// Initiate Database connection
}

//Database connection
private function dbConnect()
{
	$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
	if($this->db)
	mysql_select_db(self::DB,$this->db);
}

//Public method for access api.
//This method dynmically call the method based on the query string
public function processApi()
{
	$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
	if((int)method_exists($this,$func) > 0)
		$this->$func();
	else
		$this->response('',404); 
	// If the method not exist with in this class, response would be "Page not found".
}

	/**
	 *	Metodo para obtener el padreID de un usuario
	 */
	private function obtenerPadreID(){
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET"){
			$this->response('',406);
		}
		
		$usuarioID = $this->_request['usuarioID'];
		
		// Se validan que los parametros llegaron
		if(!empty($usuarioID)){
			
			// Se hace la consulta para obtener el padre_ID de un usuario
			$sql = mysql_query("SELECT FK_PADRE_ID FROM NG_USUARIOS WHERE USUARIO_ID = '$usuarioID'", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = mysql_fetch_array($sql,MYSQL_ASSOC);
				
				$result['codigoRespuesta'] = 1;
				// If success everythig is good send header as "OK" and user details
				$this->response($this->json($result), 200);
			}
			$error = array('status' => "Failed", "msg" => "No se encontro padre del usuario = '$usuarioID'", "codigoRespuesta" => "0");	
			$this->response($this->json($error), 200); // If no records "No Content" status		
		}
		
		// If invalid inputs "Bad Request" status message and reason
		$error = array('status' => "Failed", "msg" => "No se encontro padre", "codigoRespuesta" => "0");	
		$this->response($this->json($error), 400);
	}

	/**
	 *	Metodo encargado de obtener la lista de referidos de un usuario.
	 */
	private function obtenerReferidos(){
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET"){
			$this->response('',406);
		}
		
		$usuarioID = $this->_request['usuarioID'];
		
		// Se validan que los parametros llegaron
		if(!empty($usuarioID)){
			
			// Se hace la consulta para obtener la lista de referidos
			$sql = mysql_query("SELECT * FROM NG_USUARIOS WHERE FK_PADRE_ID = '$usuarioID'", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = array();
				$result_temp = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result_temp[] = $rlt;
				}
				
				$result['referidos'] = $result_temp;
				$result['codigoRespuesta'] = 1;
				// If success everythig is good send header as "OK" and user details
				$this->response($this->json($result), 200);
			}
			$error = array('status' => "Failed", "msg" => "No se encontraro referidos para el usuario = '$usuarioID'", "codigoRespuesta" => "0");	
			$this->response($this->json($error), 200); // If no records "No Content" status		
		}
		
		// If invalid inputs "Bad Request" status message and reason
		$error = array('status' => "Failed", "msg" => "No se encontraron referidos", "codigoRespuesta" => "0");	
		$this->response($this->json($error), 400);
	}

	/**
	 *  Metodo para realizar login de un usuario.
	 */
	private function logInUsuario()
	{
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		
		$usuario = $this->_request['usuario'];
		$clave = $this->_request['clave'];
		
		// Se validan que los parametros llegaron
		if(!empty($usuario) and !empty($clave))
		{
			// Se hace la consulta del usuario logueado
			$sql = mysql_query("SELECT U.USUARIO_ID, U.USUARIO, P.PRIMER_NOMBRE, P.SEGUNDO_NOMBRE, P.PRIMER_APELLIDO, P.SEGUNDO_APELLIDO, P.CEDULA, P.CORREO, P.TELEFONO, P.FECHA_NACIMIENTO FROM NG_USUARIOS as U, NG_PERSONAS as P WHERE U.FK_PERSONA_ID = P.PERSONA_ID AND U.ESTADO = 1 AND U.USUARIO = '$usuario' AND U.CLAVE = SHA1('$clave') LIMIT 1", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = mysql_fetch_array($sql,MYSQL_ASSOC);
				
				$result['codigoRespuesta'] = 1;
				// If success everythig is good send header as "OK" and user details
				$this->response($this->json($result), 200);
			}
			$error = array('status' => "Failed", "msg" => "Usuario ? Clave invalidos, por favor intente de nuevo", "codigoRespuesta" => "0");	
			$this->response($this->json($error), 200); // If no records "No Content" status
		}
		
		// If invalid inputs "Bad Request" status message and reason
		$error = array('status' => "Failed", "msg" => "Usuario ? Clave invalidos", "codigoRespuesta" => "0");	
		$this->response($this->json($error), 400);
	}
	
	/**
	 *  Metodo encargado de obtener el detalle de una noticia.
	 */
	private function obtenerDetalleNoticia()
	{
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		
		$noticiaID = $this->_request['noticiaID'];
		
		// Se validan que los parametros llegaron
		if(!empty($noticiaID)){
			// Se hace la consulta del usuario logueado
			$sql = mysql_query("SELECT N.TITULO, N.DESCRIPCION, N.FECHA, N.IMAGEN_DESCRIP FROM NG_NOTICIAS AS N WHERE N.NOTICIA_ID = '$noticiaID'", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = mysql_fetch_array($sql,MYSQL_ASSOC);
				
				$result['codigoRespuesta'] = 1;
				// If success everythig is good send header as "OK" and user details
				$this->response($this->json($result), 200);
			}
			$error = array('status' => "Failed", "msg" => "Noticia invalida, por favor intente de nuevo", "codigoRespuesta" => "0");	
			$this->response($this->json($error), 200); // If no records "No Content" status
		}
		
		// If invalid inputs "Bad Request" status message and reason
		$error = array('status' => "Failed", "msg" => "Noticia invalida", "codigoRespuesta" => "0");	
		$this->response($this->json($error), 400);
	}

	/**
	 *	Metodo encargado de obtener la lista de usuarios.
	 */
	private function users(){ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET"){
			$this->response('',406);
		}
		$sql = mysql_query("SELECT U.USUARIO_ID, U.USUARIO, P.PRIMER_NOMBRE, P.SEGUNDO_NOMBRE, P.PRIMER_APELLIDO, P.SEGUNDO_APELLIDO, P.CEDULA, P.CORREO, P.TELEFONO, P.FECHA_NACIMIENTO FROM NG_USUARIOS as U, NG_PERSONAS as P WHERE U.FK_PERSONA_ID = P.PERSONA_ID AND U.ESTADO = 1;", $this->db);
		if(mysql_num_rows($sql) > 0){
			$result = array();
			while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
				$result[] = $rlt;
			}
			// If success everythig is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function obtenerNoticias(){ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET"){
			$this->response('',406);
		}
		$sql = mysql_query("SELECT N.NOTICIA_ID, N.TITULO, N.PRE_DESCRIPCION, N.FECHA, N.IMAGEN FROM NG_NOTICIAS AS N Order by N.NOTICIA_ID Desc", $this->db);
		if(mysql_num_rows($sql) > 0){
			$result = array();
				$result_temp = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result_temp[] = $rlt;
				}
				
			$result['noticias'] = $result_temp;			
			$result['codigoRespuesta'] = 1;
			
			// If success everythig is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	//Encode array into JSON
	private function json($data)
	{
		if(is_array($data)){
			return json_encode($data);
		}
	}
}

// Initiiate Library
$api = new API;
$api->processApi();
?>