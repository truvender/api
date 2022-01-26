<?php

namespace Database\Seeders;

use App\Models\Variation;
use Illuminate\Database\Seeder;

class Variations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cableVariations = [
            "dstv-yanga" => "DStv Yanga",
            "dstv-padi" => "DStv Padi",
            "dstv-confam" => "DStv Confam",
            "dstv6" => "DStv Asia",
            "dstv79" => "DStv Compact",
            "dstv7" => "DStv Compact Plus",
            "dstv3" => "DStv Premium",
            "dstv10" => "DStv Premium Asia",
            "dstv9" => "DStv Premium-French",
            "confam-extra" => "DStv Confam + ExtraView",
            "yanga-extra" => "DStv Yanga + ExtraView",
            "padi-extra" => "DStv Padi + ExtraView",
            "com-asia" => "DStv Compact + Asia",
            "dstv30" => "DStv Compact + Extra View",
            "com-frenchtouch" => "DStv Compact + French Touch",
            "dstv33" => "DStv Premium – Extra View",
            "dstv40" => "DStv Compact Plus – Asia",
            "com-frenchtouch-extra" => "DStv Compact + French Touch + ExtraView",
            "com-asia-extra" => "DStv Compact + Asia + ExtraView",
            "dstv43" => "DStv Compact Plus + French Plus",
            "complus-frenchtouch" => "DStv Compact Plus + French Touch",
            "dstv45" => "DStv Compact Plus – Extra View",
            "complus-french-extraview" => "DStv Compact Plus + FrenchPlus + Extra View",
            "dstv47" => "DStv Compact + French Plus",
            "dstv48" => "DStv Compact Plus + Asia + ExtraView",
            "dstv61" => "DStv Premium + Asia + Extra View",
            "dstv62" => "DStv Premium + French + Extra View",
            "hdpvr-access-service" => "DStv HDPVR Access Service",
            "frenchplus-addon" => "DStv French Plus Add-on",
            "asia-addon" => "DStv Asian Add-on",
            "frenchtouch-addon" => "DStv French Touch Add-on",
            "extraview-access" => "ExtraView Access",
            "french11" => "DStv French 11",
            "gotv-smallie" => "GOtv Smallie",
            "gotv-jinja" => "GOtv Jinja",
            "gotv-jolli" => "GOtv Jolli",
            "gotv-max" => "GOtv Max",
            "nova" => "Startimes Nova",
            "basic" => "Startimes Basic",
            "smart" => "Startimes Smart",
            "classic" => "Startimes Classic",
            "super" => "Startimes Super",
        ];

        $dataVariations = [
            "500" => "MTN Data 500MB (SME) – 30 Days",
            "M1024" => "MTN Data 1GB (SME) – 30 Days",
            "M2024" => "MTN Data 2GB (SME) – 30 Days",
            "3000" => "MTN Data 3GB (SME) – 30 Days",
            "5000" => "MTN Data 5GB (SME) – 30 Days",
            "10000" => "MTN Data 10GB (SME) – 30 Days",
            "mtn-20hrs-1500" => "MTN Data 6GB (Direct) – 7 Days",
            "GIFT5000" => "MTN Data 20GB (Direct) – 30 Days",
            "mtn-30gb-8000" => "MTN Data 30GB (Direct) – 30 Days",
            "mtn-40gb-10000" => "MTN Data 40GB (Direct) – 30 Days",
            "mtn-75gb-15000" => "MTN Data 75GB (Direct) – 30 Days",
            "glo100x" => "Glo Data 1GB – 5 Nights",
            "glo200x" => "Glo Data 1.25GB – 1 Day (Sunday)",
            "G500" => "Glo Data 1.35GB – 14 Days",
            "G2000" => "Glo Data 5.8GB – 30 Days",
            "G1000" => "Glo Data 2.9GB – 30 Days",
            "G2500" => "Glo Data 7.7GB – 30 Days",
            "G3000" => "Glo Data 10GB – 30 Days",
            "G4000" => "Glo Data 13.25GB – 30 Days",
            "G5000" => "Glo Data 18.25GB – 30 Days",
            "G8000" => "Glo Data 29.5GB – 30 Days",
            "glo10000" => "Glo Data 50GB – 30 Days",
            "airt-500" => "Airtel Data 750MB – 14 Days",
            "airt-300x" => "Airtel Data 1GB – 1 Day",
            "AIR1000" => "Airtel Data 1.5GB – 30 Days",
            "airt-500x" => "Airtel Data 2GB – 2 Days",
            "airt-1200" => "Airtel Data 2GB – 30 Days",
            "Air1500" => "Airtel Data 3GB – 30 Days",
            "AIR2000" => "Airtel Data 4.5GB – 30 Days",
            "airt-1500-2" => "Airtel Data 6GB – 7 Days",
            "Air3000" => "Airtel Data 10GB – 30 Days",
            "Air5000" => "Airtel Data 20GB – 30 Days",
            "Air100000" => "Airtel Data 40GB – 30 Days",
            "9MOB1000" => "9mobile Data 1GB – 30 Days",
            "9MOB34500" => "9mobile Data 2.5GB – 30 Days",
            "9MOB8000" => "9mobile Data 11.5GB – 30 Days",
            "9MOB5000" => "9mobile Data 15GB – 30 Days",
        ];

        $electricVariations = [
            "abuja-electric" => "Abuja Electricity Distribution Company (AEDC)",
            "eko-electric" => "Eko Electricity Distribution Company (EKEDC)",
            "ibadan-electric" => "Ibadan Electricity Distribution Company (IBEDC)",
            "ikeja-electric" => "Ikeja Electricity Distribution Company (IKEDC)",
            "jos-electric" => "Jos Electricity Distribution PLC (JEDplc)",
            "kaduna-electric" => "Kaduna Electricity Distribution Company (KAEDCO)",
            "kano-electric" => "Kano Electricity Distribution Company (KEDCO)",
            "portharcourt-electric" => "Port Harcourt Electricity Distribution Company (PHED)",
        ];


        Variation::truncate();


        foreach ($cableVariations as $key => $name) {
            Variation::create([
                'code' => $key,
                'name' => $name,
                'type' => 'cable',
            ]);
        }

        foreach ($dataVariations as $key => $value) {
            Variation::create([
                'code' => $key,
                'name' => $value,
                'type' => 'data',
            ]);
        }


        foreach ($electricVariations as $key => $value) {
            Variation::create([
                'code' => $key,
                'name' => $value,
                'type' => 'electricity',
            ]);
        }
    }
}
