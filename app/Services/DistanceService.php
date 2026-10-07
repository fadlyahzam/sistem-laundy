<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class DistanceService
{
    public const EARTH_RADIUS_KM = 6371.0;
    public const DEFAULT_OUTLET_LAT = -6.175392;
    public const DEFAULT_OUTLET_LNG = 106.827153;
    public const MAX_RADIUS_KM = 20.0;
    public const OUT_OF_RANGE_MESSAGE = 'Maaf, lokasi Anda di luar jangkauan (Maksimal radius 20 KM).';

    /**
     * Get outlet coordinates from config or defaults.
     *
     * @return array{latitude: float, longitude: float}
     */
    public function getOutletCoordinates(): array
    {
        return [
            'latitude' => (float) Config::get('services.outlet.latitude', self::DEFAULT_OUTLET_LAT),
            'longitude' => (float) Config::get('services.outlet.longitude', self::DEFAULT_OUTLET_LNG),
        ];
    }

    /**
     * Get maximum allowed radius in KM.
     */
    public function getMaxRadius(): float
    {
        return (float) Config::get('services.outlet.max_radius_km', self::MAX_RADIUS_KM);
    }

    /**
     * Calculate distance between two coordinates using the Haversine formula.
     *
     * @param float $fromLat
     * @param float $fromLng
     * @param float $toLat
     * @param float $toLng
     * @return float Distance in Kilometers rounded to 2 decimals
     */
    public function calculateHaversineDistance(
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng
    ): float {
        $latFrom = deg2rad($fromLat);
        $lonFrom = deg2rad($fromLng);
        $latTo = deg2rad($toLat);
        $lonTo = deg2rad($toLng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        $distance = $angle * self::EARTH_RADIUS_KM;

        return round($distance, 2);
    }

    /**
     * Calculate distance between customer coordinates and the outlet.
     *
     * @param float $customerLat
     * @param float $customerLng
     * @return float Distance in KM rounded to 2 decimals
     */
    public function calculateDistanceToOutlet(float $customerLat, float $customerLng): float
    {
        $outlet = $this->getOutletCoordinates();

        return $this->calculateHaversineDistance(
            $customerLat,
            $customerLng,
            $outlet['latitude'],
            $outlet['longitude']
        );
    }

    /**
     * Check if given coordinates are within the maximum allowable radius.
     */
    public function isWithinRadius(float $customerLat, float $customerLng, ?float $maxRadius = null): bool
    {
        $maxRadius = $maxRadius ?? $this->getMaxRadius();
        $distance = $this->calculateDistanceToOutlet($customerLat, $customerLng);

        return $distance <= $maxRadius;
    }

    /**
     * Validate distance and return detailed check result.
     *
     * @return array{is_valid: bool, distance_km: float, max_radius_km: float, message: ?string}
     */
    public function validateDistance(float $customerLat, float $customerLng, ?float $maxRadius = null): array
    {
        $maxRadius = $maxRadius ?? $this->getMaxRadius();
        $distance = $this->calculateDistanceToOutlet($customerLat, $customerLng);
        $isValid = $distance <= $maxRadius;

        return [
            'is_valid' => $isValid,
            'distance_km' => $distance,
            'max_radius_km' => $maxRadius,
            'message' => $isValid ? null : self::OUT_OF_RANGE_MESSAGE,
        ];
    }

    /**
     * Ensure coordinates are within radius; throws ValidationException if out of bounds.
     *
     * @throws ValidationException
     */
    public function ensureWithinRadius(float $customerLat, float $customerLng, ?float $maxRadius = null): float
    {
        $validation = $this->validateDistance($customerLat, $customerLng, $maxRadius);

        if (!$validation['is_valid']) {
            throw ValidationException::withMessages([
                'latitude' => self::OUT_OF_RANGE_MESSAGE,
                'distance' => self::OUT_OF_RANGE_MESSAGE,
            ]);
        }

        return $validation['distance_km'];
    }
}
