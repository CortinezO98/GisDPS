<?php
    // error_reporting(E_ALL);
    require_once 'conexion/conexion.php';
    class trasladoivacaso extends conexion {
        public function insertarCaso($datos) {
            $gti_id=$datos['interaccion_id'];
            $gti_interaccion_id=$datos['interaccion_id'];
            $gti_interaccion_fecha=$datos['interaccion_fecha'];
            $gti_remitente=$datos['remitente'];
            $gti_cliente_identificacion=$datos['cliente_identificacion'];
            $gti_cliente_nombre=$datos['cliente_nombre'];
            $gti_titular_cedula=$datos['titular_cedula'];
            $gti_titular_fecha_expedicion=$datos['titular_fecha_expedicion'];
            $gti_beneficiario_identificacion=$datos['beneficiario_identificacion'];
            $gti_link_foto=$datos['link_foto'];
            $gti_departamento=$datos['departamento'];
            $gti_municipio=$datos['municipio'];
            $gti_direccion=$datos['direccion'];
            $gti_ruta_fichero=$datos['ruta_fichero'];

            $gti_estado='Pendiente';
            $gti_responsable='';
            $gti_numero_novedad='';
            $gti_observaciones='';
            $gti_fecha_gestion='';
            $gti_estado_bloqueo='0';
            $gti_fecha_bloqueo='';
            $query = "INSERT INTO `gestion_traslado_iva`(`gti_id`, `gti_interaccion_id`, `gti_interaccion_fecha`, `gti_remitente`, `gti_cliente_identificacion`, `gti_cliente_nombre`, `gti_titular_cedula`, `gti_titular_fecha_expedicion`, `gti_beneficiario_identificacion`, `gti_link_foto`, `gti_departamento`, `gti_municipio`, `gti_direccion`, `gti_ruta_fichero`, `gti_estado`, `gti_responsable`, `gti_numero_novedad`, `gti_observaciones`, `gti_fecha_gestion`, `gti_estado_bloqueo`, `gti_fecha_bloqueo`) VALUES ('".$gti_id."','".$gti_interaccion_id."','".$gti_interaccion_fecha."','".$gti_remitente."','".$gti_cliente_identificacion."','".$gti_cliente_nombre."','".$gti_titular_cedula."','".$gti_titular_fecha_expedicion."','".$gti_beneficiario_identificacion."','".$gti_link_foto."','".$gti_departamento."','".$gti_municipio."','".$gti_direccion."', '".$gti_ruta_fichero."','".$gti_estado."','".$gti_responsable."','".$gti_numero_novedad."','".$gti_observaciones."','".$gti_fecha_gestion."','".$gti_estado_bloqueo."','".$gti_fecha_bloqueo."')";
            $verifica = parent::nonQuery($query);
            if ($verifica==1) {
                return 1;
            } else {
                return 0;
            }
        }
    }
?>