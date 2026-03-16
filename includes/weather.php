<?php

/**
 * Fetch 7-day daily weather forecast from Open-Meteo (free, no key needed).
 *
 * @param float $lat
 * @param float $lon
 * @return array|false  Array of daily forecast or false on failure.
 */
function fetch_weather($lat, $lon) {
    $lat = (float)$lat;
    $lon = (float)$lon;

    $url = "https://api.open-meteo.com/v1/forecast?"
         . "latitude={$lat}&longitude={$lon}"
         . "&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum"
         . "&timezone=Asia%2FKolkata"
         . "&forecast_days=7";

    $ctx = stream_context_create([
        'http' => [
            'timeout'        => 5,
            'ignore_errors'  => true,
            'user_agent'     => 'AgriMan/1.0',
        ]
    ]);

    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) return false;

    $data = json_decode($raw, true);
    if (!isset($data['daily'])) return false;

    $daily   = $data['daily'];
    $count   = count($daily['time']);
    $result  = [];

    for ($i = 0; $i < $count; $i++) {
        $result[] = [
            'date'          => $daily['time'][$i],
            'code'          => (int)($daily['weathercode'][$i] ?? 0),
            'temp_max'      => $daily['temperature_2m_max'][$i],
            'temp_min'      => $daily['temperature_2m_min'][$i],
            'precipitation' => $daily['precipitation_sum'][$i],
        ];
    }
    return $result;
}

/**
 * Return true if the WMO weather code indicates rain.
 * Rain codes: 51-65 (drizzle/rain), 80-95 (showers/thunderstorm).
 */
function is_rain_code($code) {
    $code = (int)$code;
    return ($code >= 51 && $code <= 65) || ($code >= 80 && $code <= 95);
}

/**
 * Map a WMO code to a human-readable label + Bootstrap icon.
 */
function weather_label($code) {
    $code = (int)$code;
    $map = [
        0  => ['Clear sky',          'bi-sun',               'text-warning'],
        1  => ['Mainly clear',       'bi-sun',               'text-warning'],
        2  => ['Partly cloudy',      'bi-cloud-sun',         'text-secondary'],
        3  => ['Overcast',           'bi-clouds',            'text-secondary'],
        45 => ['Foggy',              'bi-cloud-fog2',        'text-secondary'],
        48 => ['Icy fog',            'bi-cloud-fog2',        'text-secondary'],
        51 => ['Light drizzle',      'bi-cloud-drizzle',     'text-info'],
        53 => ['Moderate drizzle',   'bi-cloud-drizzle',     'text-info'],
        55 => ['Dense drizzle',      'bi-cloud-drizzle',     'text-info'],
        61 => ['Slight rain',        'bi-cloud-rain',        'text-primary'],
        63 => ['Moderate rain',      'bi-cloud-rain',        'text-primary'],
        65 => ['Heavy rain',         'bi-cloud-rain-heavy',  'text-primary'],
        71 => ['Slight snow',        'bi-cloud-snow',        'text-info'],
        73 => ['Moderate snow',      'bi-cloud-snow',        'text-info'],
        75 => ['Heavy snow',         'bi-cloud-snow',        'text-info'],
        80 => ['Rain showers',       'bi-cloud-rain',        'text-primary'],
        81 => ['Heavy showers',      'bi-cloud-rain-heavy',  'text-primary'],
        82 => ['Violent showers',    'bi-cloud-rain-heavy',  'text-danger'],
        95 => ['Thunderstorm',       'bi-cloud-lightning',   'text-danger'],
        96 => ['Thunderstorm+hail',  'bi-cloud-lightning',   'text-danger'],
        99 => ['Severe thunderstorm','bi-cloud-lightning',   'text-danger'],
    ];
    if (isset($map[$code])) return $map[$code];
    if ($code >= 51 && $code <= 69)  return ['Rain',          'bi-cloud-rain',       'text-primary'];
    if ($code >= 70 && $code <= 79)  return ['Snow',          'bi-cloud-snow',       'text-info'];
    if ($code >= 80 && $code <= 99)  return ['Storms',        'bi-cloud-lightning',  'text-danger'];
    return ['Unknown', 'bi-question-circle', 'text-muted'];
}

function weather_by_date($forecast) {
    $map = [];
    if (!$forecast) return $map;
    foreach ($forecast as $day) {
        $map[$day['date']] = $day['code'];
    }
    return $map;
}
