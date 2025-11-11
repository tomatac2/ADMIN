<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{

    private array $citiesByState = [

        'ar-Riyad' => [
            'Riyadh', 'Al Kharj', 'Ad Dilam', 'Al Majmaah', 'Az Zulfi', 'Al Ghat', 'Shaqra', 'Ad Dawadmi',
            'Afif', 'Sajir', 'Hotat Bani Tamim', 'Al Muzahimiyah', 'Rumah', 'Thadiq', 'Huraymila', 'Diriyah',
        ],
        'Makkah' => [
            'Makkah', 'Jeddah', 'At Taif', 'Rabigh', 'Khulais', 'Al Lith', 'Al Qunfudhah', 'Al Khurma',
        ],
        'al-Madinah' => [
            'Al Madinah', 'Yanbu', 'Badr', 'Al Ula', 'Khaybar', 'Mahd adh Dhahab',
        ],
        'Ash Sharqiyah' => [
            'Dammam', 'Dhahran', 'Al Khobar', 'Qatif', 'Jubail', 'Ras Tanura', 'Hafr Al Batin', 'Khafji',
            'Nairyah', 'Qaisumah', 'Abqaiq', 'Al Hofuf', 'Al Mubarraz',
        ],
        'Asir' => [
            'Abha', 'Khamis Mushayt', 'Bisha', 'Tathlith', 'Ahad Rafidah', 'Muhayil', 'Tanumah', 'Sarat Abidah',
        ],
        'Jizan' => [
            'Jizan', 'Sabya', 'Abu Arish', 'Samtah', 'Baish', 'Al Darb',
        ],
        'Najran' => [
            'Najran', 'Sharurah', 'Hubuna', 'Badr Al Janub',
        ],
        'al-Bahah' => [
            'Al Bahah', 'Baljurashi', 'Al Mandaq', 'Al Aqiq',
        ],
        'Tabuk' => [
            'Tabuk', 'Duba', 'Al Wajh', 'Umluj', 'Tayma',
        ],
        "Ha'il" => [
            'Hail', 'Baqaa', 'Al Ghazalah', 'As Shinan',
        ],
        'Jawf' => [
            'Sakaka', 'Dumat Al Jandal', 'Qurayyat',
        ],
        'al-Hudud-ash-Shamaliyah' => [
            'Arar', 'Rafha', 'Turayf',
        ],
        'Qasim' => [
            'Buraydah', 'Unaizah', 'Ar Rass', 'Al Bukayriyah', 'Al Badayea', 'Al Mithnab', 'Uyun Al Jawa',
        ],

        'al-Qahira' => [
            'Cairo', 'New Cairo', 'Helwan', '15 May City',
        ],
        'al-Jizah' => [
            'Giza', '6th of October', 'Sheikh Zayed', 'Hawamdeya', 'Al Badrashin',
        ],
        'al-Iskandariyah' => [
            'Alexandria', 'Borg El Arab', 'Abu Qir',
        ],
        'al-Qalyubiyah' => [
            'Banha', 'Qalyub', 'Qaha', 'Shubra El Kheima', 'Al Qanatir Al Khayriyah',
        ],
        'al-Minufiyah' => [
            'Shibin El Kom', 'Menouf', 'Ashmoun', 'Tala',
        ],
        'al-Gharbiyah' => [
            'Tanta', 'El Mahalla El Kubra', 'Kafr El Zayyat', 'Zefta', 'Samanoud',
        ],
        'ad-Daqahliyah' => [
            'Mansoura', 'Mit Ghamr', 'Talkha', 'Belqas', 'Dekernes', 'Sherbin',
        ],
        'ash-Sharqiyah' => [
            'Zagazig', 'Bilbeis', '10th of Ramadan', 'Abu Hammad', 'El Husseiniya',
        ],
        'al-Buhayrah' => [
            'Damanhur', 'Kafr El Dawwar', 'Rashid', 'Etay El Barud',
        ],
        'Kafr-ash-Shaykh' => [
            'Kafr El Sheikh', 'Desouk', 'Baltim', 'Fouh',
        ],
        'Dumyat' => [
            'Damietta', 'Faraskur', 'Kafr Saad', 'Ras El Bar',
        ],
        "Bur Sa'id" => [
            'Port Said', 'Port Fuad',
        ],
        'al-Ismailiyah' => [
            'Ismailia', 'Fayed', 'Qantara Gharb', 'Qantara Sharq',
        ],
        'as-Suways' => [
            'Suez', 'Ataka', 'Ain Sokhna',
        ],
        'Sina ash-Shamaliyah' => [
            'Arish', 'Sheikh Zuweid', 'Bir El Abd', 'Rafah',
        ],
        'Sina al-Janubiyah' => [
            'Sharm El Sheikh', 'Dahab', 'Nuweiba', 'Taba', 'Saint Catherine',
        ],
        'al-Bahr-al-Ahmar' => [
            'Hurghada', 'Safaga', 'Quseir', 'Marsa Alam', 'Ras Gharib',
        ],
        'al-Fayyum' => [
            'Faiyum', 'Itsa', 'Tamiya', 'Sinnuris', 'Yusuf El Seddik',
        ],
        'Bani Suwayf' => [
            'Beni Suef', 'Al Wasta', 'Biba', 'Ihnasiya', 'Nasser',
        ],
        'al-Minya' => [
            'Minya', 'Mallawi', 'Samalut', 'Maghagha', 'Beni Mazar',
        ],
        'Asyut' => [
            'Assiut', 'Dairut', 'Manfalut', 'Abnub', 'Abu Tig',
        ],
        'Sawhaj' => [
            'Sohag', 'Tahta', 'Girga', 'Akhmim', 'Tima',
        ],
        'Qina' => [
            'Qena', 'Qus', 'Nag Hammadi', 'Qift', 'Dishna',
        ],
        'al-Uqsur' => [
            'Luxor', 'Esna', 'Armant', 'Toud',
        ],
        'Aswan' => [
            'Aswan', 'Kom Ombo', 'Edfu', 'Daraw', 'Abu Simbel',
        ],
        'al-Wadi al-Jadid' => [
            'Kharga', 'Dakhla', 'Farafra', 'Balat', 'Paris',
        ],
        'Matruh' => [
            'Marsa Matruh', 'El Alamein', 'Siwa', 'Dabaa', 'Sallum',
        ],
    ];

    private array $synonyms = [
        'Eastern Province' => 'Ash Sharqiyah',
        'Aseer' => 'Asir',
        'Central Province' => 'ar-Riyad',
        'Western Province' => 'Makkah',
        'Cairo' => 'al-Qahira',
        'Muhafazat al Qahirah' => 'al-Qahira',
        'Muhafazat al Iskandariyah' => 'al-Iskandariyah',
        'Muhafazat al Gharbiyah' => 'al-Gharbiyah',
        'Muhafazat ad Daqahliyah' => 'ad-Daqahliyah',
        'Muhafazat al Fayyum' => 'al-Fayyum',
        'Muhafazat al Ismailiyah' => 'al-Ismailiyah',
        'Muhafazat al Qalyubiyah' => 'al-Qalyubiyah',
        'Muhafazat al Minufiyah' => 'al-Minufiyah',
        'al-Qahirah' => 'al-Qahira',
    ];

    public function run(): void
    {
        foreach ($this->citiesByState as $stateName => $cities) {
            $state = $this->resolveState($stateName);

            if (!$state) {
                continue;
            }

            foreach ($cities as $cityName) {
                $exists = City::where('name', $cityName)
                    ->where('state_id', $state->id)
                    ->first();

                if ($exists) {
                    continue;
                }

                City::create([
                    'name' => $cityName,
                    'state_id' => $state->id,
                ]);
            }
        }

        $this->command?->info('✅ CitySeeder: Success');
    }

    private function resolveState(string $name): ?State
    {
        $state = State::where('name', $name)->first();
        if ($state) return $state;

        if (isset($this->synonyms[$name])) {
            $alt = $this->synonyms[$name];
            $state = State::where('name', $alt)->first();
            if ($state) return $state;
        }

        $state = State::where('name', 'LIKE', "%{$name}%")->first();

        return $state;
    }
}
