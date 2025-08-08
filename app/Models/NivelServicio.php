<?php

namespace App\Models;

class NivelServicio extends BaseModel
{
    protected $table = 'tk_nivel_servicio';

    protected $primaryKey = 'IDNivelServicio';

    protected $guarded = [];

    protected $appends = ['tiempo_solucion', 'tiempo_respuesta'];

    public function getTiempoSolucionAttribute()
    {
        $tiempo = $this->TSolucionNivelServicio;
        $tiempoFormateado = $this->formatTiempo($tiempo);

        return $tiempoFormateado;
    }

    public function getTiempoRespuestaAttribute()
    {
        $tiempo = $this->TRespuestaNivelServicio;
        $tiempoFormateado = $this->formatTiempo($tiempo);

        return $tiempoFormateado;
    }

    public function formatTiempo($tiempo)
    {
        // Si es menos de 60 minutos
        if ($tiempo < 60) {
            return $tiempo == 1 ? '1 minuto' : "$tiempo minutos";
        }
        // Si es menos de 24 horas
        elseif ($tiempo < 1440) {
            $horas = floor($tiempo / 60);
            $minutos = $tiempo % 60;

            $resultado = $horas == 1 ? '1 hora' : "$horas horas";
            if ($minutos > 0) {
                $resultado .= $minutos == 1 ? ' con 1 minuto' : " con $minutos minutos";
            }

            return $resultado;
        }
        // Si es menos de 365 días
        elseif ($tiempo < 525600) {
            $dias = floor($tiempo / 1440);
            $horasMinutos = $tiempo % 1440;

            $resultado = $dias == 1 ? '1 día' : "$dias días";

            if ($horasMinutos > 0) {
                $horas = floor($horasMinutos / 60);
                $minutos = $horasMinutos % 60;

                if ($horas > 0) {
                    $resultado .= $horas == 1 ? ' con 1 hora' : " con $horas horas";

                    if ($minutos > 0) {
                        $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                    }
                } elseif ($minutos > 0) {
                    $resultado .= $minutos == 1 ? ' con 1 minuto' : " con $minutos minutos";
                }
            }

            return $resultado;
        }
        // Si es más de 365 días (años)
        else {
            $años = floor($tiempo / 525600);
            $diasMinutos = $tiempo % 525600;

            $resultado = $años == 1 ? '1 año' : "$años años";

            if ($diasMinutos > 0) {
                $dias = floor($diasMinutos / 1440);

                if ($dias > 0) {
                    $resultado .= $dias == 1 ? ' con 1 día' : " con $dias días";

                    $horasMinutos = $diasMinutos % 1440;
                    if ($horasMinutos > 0) {
                        $horas = floor($horasMinutos / 60);
                        $minutos = $horasMinutos % 60;

                        if ($horas > 0) {
                            $resultado .= $horas == 1 ? ', 1 hora' : ", $horas horas";

                            if ($minutos > 0) {
                                $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                            }
                        } elseif ($minutos > 0) {
                            $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                        }
                    }
                }
            }

            return $resultado;
        }
    }
}
