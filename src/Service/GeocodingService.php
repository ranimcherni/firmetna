<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private const NOMINATIM_API_URL = 'https://nominatim.openstreetmap.org/search';

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    /**
     * Géocode une adresse et retourne les coordonnées (latitude, longitude).
     *
     * @param string $adresse Adresse complète (ex: "Avenue Habib Bourguiba, Tunis")
     * @param string|null $ville Ville optionnelle
     *
     * @return array|null ['lat' => float, 'lon' => float] ou null si non trouvé
     */
    public function geocode(string $adresse, ?string $ville = null): ?array
    {
        $query = trim($adresse);
        if ($ville) {
            $query .= ', ' . trim($ville);
        }
        $query .= ', Tunisia'; // Ajouter le pays pour améliorer les résultats

        try {
            $response = $this->httpClient->request('GET', self::NOMINATIM_API_URL, [
                'query' => [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'FIRMETNA/1.0 (Contact: noreply@firmetna.com)',
                ],
            ]);

            $data = $response->toArray();

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lon' => (float) $data[0]['lon'],
                ];
            }
        } catch (\Exception $e) {
            // En cas d'erreur, retourner null
            return null;
        }

        return null;
    }
}
