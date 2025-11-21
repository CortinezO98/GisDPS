<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión OCR";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_id`=?";
    $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
    $consulta_registros_caso->bind_param("s", $id_registro);
    $consulta_registros_caso->execute();
    $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_cod_familia`=? ORDER BY `ocrc_registro_fecha` ASC";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $resultado_registros_caso[0][1]);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_historial="SELECT `gora_id`, `gora_codfamilia`, `gora_estado`, `gora_observaciones`, `gora_registro_usuario`, `gora_registro_fecha`, TUR.`usu_nombres_apellidos`, `gora_llamada_tipificacion`, `gora_llamada_id` FROM `gestion_ocr_resultado_avances` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_ocr_resultado_avances`.`gora_registro_usuario`=TUR.`usu_id` WHERE `gora_codfamilia`=? ORDER BY `gora_registro_fecha` DESC";
    $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
    $consulta_registros_historial->bind_param("s", $resultado_registros_caso[0][1]);
    $consulta_registros_historial->execute();
    $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM); 

    $control_sr=0;
    for ($i=0; $i < count($resultado_registros); $i++) {
      if ($resultado_registros[$i][25]=='No validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-Agente-SR' OR $resultado_registros[$i][25]=='Validado-Edad-SR') {
        $control_sr++;
      }
    }
?>
<div class="row px-4 py-2">
    <div class="col-md-12">
        <div class="row">
          <div class="col-md-12 p-1">
            <?php if($control_sr==0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                          <td colspan="17" class="p-1 alert">Primera Revisión OCR</td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Cód. Familia</th>
                            <th class="px-1 py-2">Cód. Beneficiario</th>
                            <th class="px-1 py-2">Cabeza Familia</th>
                            <th class="px-1 py-2">Documento</th>
                            <th class="px-1 py-2">Nombres y Apellidos</th>
                            <th class="px-1 py-2">Estado</th>
                            <th class="px-1 py-2">Novedad</th>
                            <th class="px-1 py-2">Contrato Existe</th>
                            <th class="px-1 py-2">Contrato Nombre Titular</th>
                            <th class="px-1 py-2">Contrato Firma</th>
                            <th class="px-1 py-2">Contrato Huella</th>
                            <th class="px-1 py-2">Documento</th>
                            <th class="px-1 py-2">Nombres</th>
                            <th class="px-1 py-2">Apellidos</th>
                            <th class="px-1 py-2">Fecha Nacimiento</th>
                            <th class="px-1 py-2">Fecha Expedición</th>
                            <th class="px-1 py-2">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                          <?php if($resultado_registros[$i][25]!='No validado-OCR-SR' AND $resultado_registros[$i][25]!='Validado-OCR-SR' AND $resultado_registros[$i][25]!='Validado-Agente-SR' AND $resultado_registros[$i][25]!='Validado-Edad-SR'): ?>
                          <tr>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                              <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                              <td class="p-1 font-size-11">
                                  <?php echo $resultado_registros[$i][28]; ?>
                                  <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                                  <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                                  <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                              </td>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][25]; ?></td>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][26]; ?></td>
                              <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                      <?php if ($resultado_registros[$i][17]==1): ?>
                                        <span class='fas fa-check-circle color-verde'></span>
                                      <?php elseif ($resultado_registros[$i][17]==''): ?>
                                        <span class='fas fa-times-circle color-rojo'></span>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                      <?php if ($resultado_registros[$i][19]!='' AND $resultado_registros[$i][19]!='NA'): ?>
                                        <span class='fas fa-check-circle color-verde'></span>
                                      <?php elseif ($resultado_registros[$i][19]==''): ?>
                                        <span class='fas fa-times-circle color-rojo'></span>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                      <?php if ($resultado_registros[$i][22]==1): ?>
                                        <span class='fas fa-check-circle color-verde'></span>
                                      <?php elseif ($resultado_registros[$i][22]==''): ?>
                                        <span class='fas fa-times-circle color-rojo'></span>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                      <?php if ($resultado_registros[$i][23]==1): ?>
                                        <span class='fas fa-check-circle color-verde'></span>
                                      <?php elseif ($resultado_registros[$i][23]==''): ?>
                                        <span class='fas fa-times-circle color-rojo'></span>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][6]==1): ?>
                                      <span class='fas fa-check-circle color-verde'></span>
                                    <?php elseif ($resultado_registros[$i][6]==''): ?>
                                      <span class='fas fa-times-circle color-rojo'></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][9]==1): ?>
                                      <span class='fas fa-check-circle color-verde'></span>
                                    <?php elseif ($resultado_registros[$i][9]==''): ?>
                                      <span class='fas fa-times-circle color-rojo'></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][11]==1): ?>
                                      <span class='fas fa-check-circle color-verde'></span>
                                    <?php elseif ($resultado_registros[$i][11]==''): ?>
                                      <span class='fas fa-times-circle color-rojo'></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][13]==1): ?>
                                      <span class='fas fa-check-circle color-verde'></span>
                                    <?php elseif ($resultado_registros[$i][13]==''): ?>
                                      <span class='fas fa-times-circle color-rojo'></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-1 font-size-11 text-center">
                                    <?php if ($resultado_registros[$i][15]==1): ?>
                                      <span class='fas fa-check-circle color-verde'></span>
                                    <?php elseif ($resultado_registros[$i][15]==''): ?>
                                      <span class='fas fa-times-circle color-rojo'></span>
                                    <?php endif; ?>
                                </td>
                              <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
                          </tr>
                          <?php endif; ?>
                      <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <?php if($control_sr>0): ?>
              <div class="table-responsive mt-1">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                          <td colspan="17" class="p-1 alert">Segunda Revisión OCR</td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Cód. Familia</th>
                            <th class="px-1 py-2">Cód. Beneficiario</th>
                            <th class="px-1 py-2">Cabeza Familia</th>
                            <th class="px-1 py-2">Documento</th>
                            <th class="px-1 py-2">Nombres y Apellidos</th>
                            <th class="px-1 py-2">Estado</th>
                            <th class="px-1 py-2">Novedad</th>
                            <th class="px-1 py-2">Contrato Existe</th>
                            <th class="px-1 py-2">Contrato Nombre Titular</th>
                            <th class="px-1 py-2">Contrato Firma</th>
                            <th class="px-1 py-2">Contrato Huella</th>
                            <th class="px-1 py-2">Documento</th>
                            <th class="px-1 py-2">Nombres</th>
                            <th class="px-1 py-2">Apellidos</th>
                            <th class="px-1 py-2">Fecha Nacimiento</th>
                            <th class="px-1 py-2">Fecha Expedición</th>
                            <th class="px-1 py-2">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                        <?php if($resultado_registros[$i][25]=='No validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-Agente-SR' OR $resultado_registros[$i][25]=='Validado-Edad-SR'): ?>
                        <tr>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                            <td class="p-1 font-size-11">
                                <?php echo $resultado_registros[$i][28]; ?>
                                <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                                <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                                <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][25]; ?></td>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][26]; ?></td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                  <?php if ($resultado_registros[$i][17]==1): ?>
                                    <span class='fas fa-check-circle color-verde'></span>
                                  <?php elseif ($resultado_registros[$i][17]==''): ?>
                                    <span class='fas fa-times-circle color-rojo'></span>
                                  <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                  <?php if ($resultado_registros[$i][19]!='' AND $resultado_registros[$i][19]!='NA'): ?>
                                    <span class='fas fa-check-circle color-verde'></span>
                                  <?php elseif ($resultado_registros[$i][19]==''): ?>
                                    <span class='fas fa-times-circle color-rojo'></span>
                                  <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                  <?php if ($resultado_registros[$i][22]==1): ?>
                                    <span class='fas fa-check-circle color-verde'></span>
                                  <?php elseif ($resultado_registros[$i][22]==''): ?>
                                    <span class='fas fa-times-circle color-rojo'></span>
                                  <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                  <?php if ($resultado_registros[$i][23]==1): ?>
                                    <span class='fas fa-check-circle color-verde'></span>
                                  <?php elseif ($resultado_registros[$i][23]==''): ?>
                                    <span class='fas fa-times-circle color-rojo'></span>
                                  <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][6]==1): ?>
                                  <span class='fas fa-check-circle color-verde'></span>
                                <?php elseif ($resultado_registros[$i][6]==''): ?>
                                  <span class='fas fa-times-circle color-rojo'></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][9]==1): ?>
                                  <span class='fas fa-check-circle color-verde'></span>
                                <?php elseif ($resultado_registros[$i][9]==''): ?>
                                  <span class='fas fa-times-circle color-rojo'></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][11]==1): ?>
                                  <span class='fas fa-check-circle color-verde'></span>
                                <?php elseif ($resultado_registros[$i][11]==''): ?>
                                  <span class='fas fa-times-circle color-rojo'></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][13]==1): ?>
                                  <span class='fas fa-check-circle color-verde'></span>
                                <?php elseif ($resultado_registros[$i][13]==''): ?>
                                  <span class='fas fa-times-circle color-rojo'></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center">
                                <?php if ($resultado_registros[$i][15]==1): ?>
                                  <span class='fas fa-check-circle color-verde'></span>
                                <?php elseif ($resultado_registros[$i][15]==''): ?>
                                  <span class='fas fa-times-circle color-rojo'></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
                        </tr>
                      <?php endif; ?>
                      <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
          </div>
        </div>
    </div>
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12 p-1">
          <p class="alert background-principal color-blanco py-1 px-2 my-1"><span class="fas fa-history"></span> Historial de Gestión</p>
            <?php if (count($resultado_registros_historial)>0): ?>
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th class="p-1 font-size-11">Estado</th>
                            <th class="p-1 font-size-11">Tipificación</th>
                            <th class="p-1 font-size-11">Id Llamada</th>
                            <th class="p-1 font-size-11">Observaciones</th>
                            <th class="p-1 font-size-11">Usuario Registro</th>
                            <th class="p-1 font-size-11">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for ($i=0; $i < count($resultado_registros_historial); $i++) { 
                        ?>
                        <tr>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][2]; ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][7]; ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][8]; ?></td>
                            <td class="p-1 font-size-11"><?php echo nl2br($resultado_registros_historial[$i][3]); ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][6]; ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][5]; ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-warning p-1 font-size-11">
                    <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                </p>
            <?php endif; ?>
        </div>
      </div>
    </div>
</div>