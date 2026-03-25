<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PortfolioSeeder extends Seeder
{
    /**
     * Download a placeholder image and store it locally.
     */
    private function downloadPlaceholder(string $filename, int $width, int $height, string $bg, string $text): string
    {
        // ✅ /png at the end forces actual PNG output
        $url      = "https://placehold.co/{$width}x{$height}/{$bg}/ffffff/png?text=" . urlencode($text);
        $response = Http::timeout(10)->get($url);
        $path     = "portfolio/thumbnails/{$filename}";
        Storage::disk('public')->put($path, $response->body());
        return $path;
    }

    public function run(): void
    {
        $items = [
            [
                'title'       => 'Dell Inspiron 15 Complete Overhaul',
                'description' => 'Full hardware diagnosis and restoration of a Dell Inspiron 15 — replaced faulty RAM, reapplied thermal paste, upgraded storage from HDD to NVMe SSD, and performed a clean OS reinstallation with all drivers.',
                'thumb_file'  => 'laptop-overhaul.png',
                'thumb_bg'    => '0d2820',
                'thumb_text'  => 'Laptop Overhaul',
                'services'    => [
                    ['name' => 'Repair',   'color' => 'red'],
                    ['name' => 'Upgrade',  'color' => 'blue'],
                ],
                'order' => 1,
            ],
            [
                'title'       => 'HP LaserJet Pro M404 Paper Jam Repair',
                'description' => 'Diagnosed and resolved persistent paper jam issues caused by worn-out pickup rollers and a misaligned paper tray. Replaced all worn components and calibrated the paper feed mechanism.',
                'thumb_file'  => 'printer-repair.png',
                'thumb_bg'    => '1a1a2e',
                'thumb_text'  => 'Printer Repair',
                'services'    => [
                    ['name' => 'Repair',       'color' => 'red'],
                    ['name' => 'Maintenance',  'color' => 'yellow'],
                ],
                'order' => 2,
            ],
            [
                'title'       => 'Samsung WF45T Front-Load Washer Fix',
                'description' => 'Resolved an E3 error code caused by a defective drain pump and clogged filter. Replaced the drain pump assembly, cleaned the filter housing, and ran full diagnostic cycles to confirm proper operation.',
                'thumb_file'  => 'washer-repair.png',
                'thumb_bg'    => '16213e',
                'thumb_text'  => 'Washer Repair',
                'services'    => [
                    ['name' => 'Repair',      'color' => 'red'],
                    ['name' => 'Maintenance', 'color' => 'yellow'],
                ],
                'order' => 3,
            ],
            [
                'title'       => 'iPhone 14 Pro Screen & Battery Replacement',
                'description' => 'Replaced a shattered OLED display and degraded battery (67% health) on an iPhone 14 Pro. Used OEM-grade parts, tested touch responsiveness, Face ID calibration, and battery health post-repair.',
                'thumb_file'  => 'iphone-repair.png',
                'thumb_bg'    => '0f3460',
                'thumb_text'  => 'iPhone Repair',
                'services'    => [
                    ['name' => 'Repair', 'color' => 'red'],
                ],
                'order' => 4,
            ],
            [
                'title'       => '8-Channel CCTV System — Commercial Property',
                'description' => 'Designed and installed a full 8-channel IP CCTV system across a 3-floor commercial building. Included PoE cameras, NVR setup, remote viewing configuration, and cable management throughout the premises.',
                'thumb_file'  => 'cctv-install.png',
                'thumb_bg'    => '2c003e',
                'thumb_text'  => 'CCTV Install',
                'services'    => [
                    ['name' => 'Installation', 'color' => 'blue'],
                    ['name' => 'Maintenance',  'color' => 'yellow'],
                ],
                'order' => 5,
            ],
            [
                'title'       => '6.5kW Residential Solar Panel System',
                'description' => 'Installed a 6.5kW grid-tied solar power system for a residential property. Work included panel mounting, inverter wiring, net metering setup, and compliance with local utility regulations.',
                'thumb_file'  => 'solar-install.png',
                'thumb_bg'    => '1b4332',
                'thumb_text'  => 'Solar Install',
                'services'    => [
                    ['name' => 'Installation', 'color' => 'blue'],
                    ['name' => 'Maintenance',  'color' => 'yellow'],
                ],
                'order' => 6,
            ],
            [
                'title'       => 'Dental Clinic Equipment Calibration',
                'description' => 'Performed full calibration and preventive maintenance on dental X-ray units, autoclave sterilizers, and patient chairs at a multi-chair dental clinic. All equipment certified compliant with safety standards.',
                'thumb_file'  => 'clinic-equipment.png',
                'thumb_bg'    => '003049',
                'thumb_text'  => 'Clinic Equipment',
                'services'    => [
                    ['name' => 'Maintenance', 'color' => 'yellow'],
                ],
                'order' => 7,
            ],
            [
                'title'       => 'Office Network Infrastructure — 30-Seat Setup',
                'description' => 'Built a complete office network for a 30-seat team including structured cabling, managed switch configuration, VLAN segmentation, firewall setup, and Wi-Fi 6 access point deployment.',
                'thumb_file'  => 'network-setup.png',
                'thumb_bg'    => '212529',
                'thumb_text'  => 'Network Setup',
                'services'    => [
                    ['name' => 'Installation', 'color' => 'blue'],
                    ['name' => 'Upgrade',      'color' => 'blue'],
                ],
                'order' => 8,
            ],
            [
                'title'       => 'MacBook Pro Liquid Damage Recovery',
                'description' => 'Recovered a liquid-damaged MacBook Pro 2021 — cleaned the logic board with ultrasonic cleaning, replaced corroded components, restored full keyboard and trackpad functionality, and recovered all user data.',
                'thumb_file'  => 'macbook-repair.png',
                'thumb_bg'    => '343a40',
                'thumb_text'  => 'MacBook Repair',
                'services'    => [
                    ['name' => 'Repair', 'color' => 'red'],
                ],
                'order' => 9,
            ],
            [
                'title'       => 'Epson L3210 Ink System Overhaul',
                'description' => 'Resolved ink clogging and print head failure on an Epson L3210 EcoTank printer. Flushed the entire ink delivery system, replaced the print head, ran nozzle check alignments, and restored print quality to factory spec.',
                'thumb_file'  => 'epson-repair.png',
                'thumb_bg'    => '023e8a',
                'thumb_text'  => 'Epson Repair',
                'services'    => [
                    ['name' => 'Repair',      'color' => 'red'],
                    ['name' => 'Maintenance', 'color' => 'yellow'],
                ],
                'order' => 10,
            ],
        ];

        foreach ($items as $item) {
            $thumbnailPath = $this->downloadPlaceholder(
                $item['thumb_file'],
                380,
                220,
                $item['thumb_bg'],
                $item['thumb_text'],
            );

            Portfolio::create([
                'title'       => $item['title'],
                'description' => $item['description'],
                'thumbnail'   => $thumbnailPath,
                'services'    => $item['services'],
                'status'      => 'published',
                'order'       => $item['order'],
            ]);
        }

        $this->command->info('✅ Portfolio seeded with ' . count($items) . ' items.');
    }
}