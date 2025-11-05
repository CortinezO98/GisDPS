<div class="col-md-7 pt-1">
    <p class="paginacion_descripcion">Registros <?php if($registros_cantidad_total>0){ echo ($pagina*$registros_x_pagina)-$registros_x_pagina+1;}else{echo "0";} ?> a <?php if(($pagina*$registros_x_pagina)>$registros_cantidad_total) { echo $registros_cantidad_total; } else { echo $pagina*$registros_x_pagina; } ?> de <?php echo $registros_cantidad_total; ?></p>
</div>
<div class="col-md-5 pt-1 text-end">
    <nav aria-label="Paginación" class="paginacion">
        <ul class="pagination justify-content-end m-0">
            <li class="page-item <?php echo $pagina<=1 ? 'disabled':'' ?>"><a class="page-link font-size-11" href="<?php echo $url_fichero; ?>?pagina=1&id=<?php echo $filtro_permanente; ?><?php echo $parametros_add; ?>"><span class="fas fa-angle-double-left"></span></a></li>
            <li class="page-item <?php echo $pagina<=1 ? 'disabled':'' ?>"><a class="page-link font-size-11" href="<?php echo $url_fichero; ?>?pagina=<?php echo $pagina-1; ?>&id=<?php echo $filtro_permanente; ?><?php echo $parametros_add; ?>"><span class="fas fa-angle-left"></span></a></li>
            <?php
                if ($numero_paginas<=5 OR $pagina<=3) {
                    $pagina_inicio=1; $pagina_fin=$numero_paginas;
                    if ($pagina<=3 AND $numero_paginas>=5) {
                        $pagina_fin=5;
                    }
                } else {
                    $pagina_inicio=$pagina-2; $pagina_fin=$pagina+2;
                    if (($numero_paginas-$pagina_inicio)<=5) {
                        $pagina_inicio=$numero_paginas-4; $pagina_fin=$numero_paginas;
                    }
                }
            ?>
            <?php for ($i=$pagina_inicio; $i <= $pagina_fin; $i++): ?>
                <li class="page-item <?php echo $pagina==$i ? 'active':'' ?>"><a class="page-link btn-corp font-size-11" href="<?php echo $url_fichero; ?>?pagina=<?php echo $i; ?>&id=<?php echo $filtro_permanente; ?><?php echo $parametros_add; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo $pagina>=$numero_paginas ? 'disabled':'' ?>"><a class="page-link font-size-11" href="<?php echo $url_fichero; ?>?pagina=<?php echo $pagina+1; ?>&id=<?php echo $filtro_permanente; ?><?php echo $parametros_add; ?>"><span class="fas fa-angle-right"></a></li>
            <li class="page-item <?php echo $pagina>=$numero_paginas ? 'disabled':'' ?>"><a class="page-link font-size-11" href="<?php echo $url_fichero; ?>?pagina=<?php echo $numero_paginas; ?>&id=<?php echo $filtro_permanente; ?><?php echo $parametros_add; ?>"><span class="fas fa-angle-double-right"></a></li>
        </ul>
    </nav>
</div>