<?php
class precios_combustibles_nicaragua_widget extends WP_Widget 
{
    function precios_combustibles_nicaragua_widget()
    {
        $configuracion = array(
            'classname' => 'precios_combustibles_nicaragua_widget', 
            'description' => __('Precios de los combustibles en Nicaragua. Información sobre el precio por litro de gasolina regular, gasolina súper, diesel y kerosene.', 'precios-de-combustibles-nicaragua') 
        );
        $this->WP_Widget('precios_combustibles_nicaragua_widget', __('Precios de los combustibles Nicaragua', 'precios-de-combustibles-nicaragua'), $configuracion);
    }

    function widget($args, $instance)
    {
        echo $args['before_widget'];

        $servicio = wp_remote_get( 'https://api.binarylemon.net/servicios/combustibles' );        
        $estado   = wp_remote_retrieve_response_code( $servicio );
        $json     = json_decode( wp_remote_retrieve_body( $servicio ), true );
 
        $fecha_hoy   = date('Y-m-d');           
        $gas_regular = 0.00;
        $gas_super   = 0.00;
        $diesel      = 0.00;
        $kerosene    = 0.00;

        if ( 200 == $estado )
        {
            foreach($json as $key => $r)
            {
                if ($key === array_key_last($json)) 
                {
                    $fecha_hoy   = $r['fecha'];         
                    $gas_regular = $r['gas_regular'];   
                    $gas_super   = $r['gas_super']; 
                    $diesel      = $r['diesel'];    
                    $kerosene    = $r['kerosene'];  
                } 
            }
        } 
        ?>

        <div class="c-container">
            <div class="c-header"> 
                <img width="50px" style="float: left; margin-right: 10px;margin-top: 4px;" src="<?php esc_url( _e( plugin_dir_url( __FILE__ ).'img/gas-pump-solid.svg') );?>" alt="">           
                <h5 class="c-title">                    
                    <?php $titulo = (empty($instance["precios_combustibles_nicaragua_titulo"])) ? __('PRECIOS DE LOS COMBUSTIBLES' , 'precios-de-combustibles-nicaragua') : $instance["precios_combustibles_nicaragua_titulo"]; ?>
                    <b><?php esc_html_e( $titulo ); ?></b>
                </h5>
                <p class="c-sub-title-1">ACTUALIZACIÓN <?=$this->pc_formatear_fecha($fecha_hoy);?></p>                 
            </div>
            <div class="c-body n-text-justify">
                <?php if(!empty($instance["precios_combustibles_nicaragua_activar"])){ ?>
                    <div id="combustibles_chart" style="width: 100%; height: auto;"></div>
                <?php } ?> 
                
                <div style="background-color: #F5F5F5; border-radius: 6px;padding: 15px;margin-top: 10px;">                   
                    <table class="c-table" cellspacing="0" cellpadding="0">
                        <tr class="c-tr">
                            <td class="c-td-left">
                                <div class="c-circulo c-color-blue"></div>
                                <?php _e('Gasolina regular', 'precios-de-combustibles-nicaragua'); ?> 
                            </td>
                            <td class="c-td-right c-right">
                                <strong>C$ <?php esc_html_e( $gas_regular );?></strong>
                            </td>
                        </tr>
                        <tr class="c-tr">
                            <td class="c-td-left">
                                <div class="c-circulo c-color-red"></div>
                                <?php _e('Gasolina súper', 'precios-de-combustibles-nicaragua'); ?> 
                            </td>
                            <td class="c-td-right c-right">
                                <strong>C$ <?php esc_html_e( $gas_super );?></strong>
                            </td>
                        </tr>
                        <tr class="c-tr">
                            <td class="c-td-left">
                                <div class="c-circulo c-color-yellow"></div>
                                <?php _e('Diesel', 'precios-de-combustibles-nicaragua'); ?> 
                            </td>
                            <td class="c-td-right c-right">
                                <strong>C$ <?php esc_html_e( $diesel );?></strong>
                            </td>
                        </tr>
                        <tr class="c-tr">
                            <td class="c-td-left">
                                <div class="c-circulo c-color-green"></div>
                                <?php _e('Kerosene', 'precios-de-combustibles-nicaragua'); ?> 
                            </td>
                            <td class="c-td-right c-right">
                                <strong>C$ <?php esc_html_e( $kerosene );?></strong>
                            </td>
                        </tr>
                    </table>
                </div>            
         
                <? if ( 200 == $estado ) { ?>
                    <script type="text/javascript">
                      google.charts.load('current', {'packages':['corechart']});
                      google.charts.setOnLoadCallback(drawChart);

                      function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Fecha', 'Gas. Regular', 'Gas. Super', 'Diesel', 'Kerosene'],
                            <? foreach ($json as $r) { ?>                   
                                ['<?=$this->pc_formatear_fecha($r["fecha"]);?>', <?=number_format($r["gas_regular"], 2);?>, <?=number_format($r["gas_super"], 2);?>, <?=number_format($r["diesel"], 2);?>, <?=number_format($r["kerosene"], 2);?>],
                            <? } ?>
                        ]);
                        var options = {
                            curveType: 'function',
                            legend: { position: 'bottom' },
                            legend: 'none',
                            chartArea:{
                                left:25,
                                right:0,
                                bottom:5,
                                top:5,
                            }
                        };
                        var chart = new google.visualization.LineChart(document.getElementById('combustibles_chart'));
                        chart.draw(data, options);
                      }
                    </script> 
                <?} ?>                                           
            </div>      
            <div class="c-footer">
                <small class="c-small"><?php _e('Fuente: Instituto Nicaragüense de Energía', 'precios-de-combustibles-nicaragua'); ?></small> 
            </div>
        </div>
        <?php
        echo $args['after_widget']; 
    }

    function update($precios_combustibles_nicaragua_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance["precios_combustibles_nicaragua_titulo"]  = sanitize_text_field($precios_combustibles_nicaragua_instance["precios_combustibles_nicaragua_titulo"]);
        $instance["precios_combustibles_nicaragua_activar"] = $precios_combustibles_nicaragua_instance["precios_combustibles_nicaragua_activar"];
        return $instance;
    }

    function form($instance)
    {
    ?>
        <div>
            <label for="<? esc_attr_e($this->get_field_id('precios_combustibles_nicaragua_titulo')); ?>"><b><?php _e('Título', 'precios-de-combustibles-nicaragua'); ?>:</b> </label>
            <input id="<?php esc_attr_e($this->get_field_id('precios_combustibles_nicaragua_titulo')); ?>" name="<?php esc_attr_e($this->get_field_name('precios_combustibles_nicaragua_titulo')); ?>" type="text" value="<?php esc_attr_e($instance["precios_combustibles_nicaragua_titulo"]); ?>" />
        </div>

        <?php $precios_combustibles_nicaragua_activar = isset( $instance['precios_combustibles_nicaragua_activar'] ) ? $instance['precios_combustibles_nicaragua_activar'] : 1; ?>

        <div>
            <label for="<?php esc_attr_e($this->get_field_id('precios_combustibles_nicaragua_activar')); ?>"><b><?php _e('Gráfico', 'precios-de-combustibles-nicaragua'); ?>:</b></label><br>
            <?php $v_activar = 1; ?>
            <input value="<?php esc_attr_e($v_activar); ?>" id="<?php esc_attr_e($this->get_field_id($v_activar)); ?>" name="<?php esc_attr_e($this->get_field_name('precios_combustibles_nicaragua_activar')); ?>" type="radio" <?php esc_attr_e($precios_combustibles_nicaragua_activar == $v_activar ? ' checked="checked"' : ''); ?> />
            <label for="<?php esc_attr_e($this->get_field_id($v_activar)); ?>"><?php _e('Habilitar', 'precios-de-combustibles-nicaragua'); ?></label>
            <br/>

            <?php $v_activar = 0; ?>
            <input value="<?php esc_attr_e($v_activar); ?>" id="<?php esc_attr_e($this->get_field_id($v_activar)); ?>" name="<?php esc_attr_e($this->get_field_name('precios_combustibles_nicaragua_activar')); ?>" type="radio" <?php esc_attr_e($precios_combustibles_nicaragua_activar == $v_activar ? ' checked="checked"' : ''); ?> />
            <label for="<?php esc_attr_e($this->get_field_id($v_activar)); ?>"><?php _e('Deshabilitar', 'precios-de-combustibles-nicaragua'); ?></label>
            <br/>
        </div>
    <?php
    }

    function pc_formatear_fecha($fecha)
    {
        $f = explode('-', $fecha);
  
        switch ($f[1])
        {
            case 1:  $nombre_mes = __('ENE', 'precios-de-combustibles-nicaragua'); break;
            case 2:  $nombre_mes = __('FEB', 'precios-de-combustibles-nicaragua'); break;
            case 3:  $nombre_mes = __('MAR', 'precios-de-combustibles-nicaragua'); break;
            case 4:  $nombre_mes = __('ABR', 'precios-de-combustibles-nicaragua'); break;
            case 5:  $nombre_mes = __('MAY', 'precios-de-combustibles-nicaragua'); break;
            case 6:  $nombre_mes = __('JUN', 'precios-de-combustibles-nicaragua'); break;
            case 7:  $nombre_mes = __('JUL', 'precios-de-combustibles-nicaragua'); break;
            case 8:  $nombre_mes = __('AGO', 'precios-de-combustibles-nicaragua'); break;
            case 9:  $nombre_mes = __('SEP', 'precios-de-combustibles-nicaragua'); break;
            case 10: $nombre_mes = __('OCT', 'precios-de-combustibles-nicaragua'); break;
            case 11: $nombre_mes = __('NOV', 'precios-de-combustibles-nicaragua'); break;
            case 12: $nombre_mes = __('DIC', 'precios-de-combustibles-nicaragua'); break;
            default: $nombre_mes = __('MES', 'precios-de-combustibles-nicaragua'); break;
        }

        return $f[2].'-'.$nombre_mes.'-'.$f[0];
    }
}
?>