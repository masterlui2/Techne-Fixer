<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ScopeOfWork;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Service Types
        $repair = ServiceType::create([
            'name' => 'Repair',
            'slug' => 'repair',
            'color' => '#ef4444',
            'icon' => '🔧',
            'status' => 'active',
            'order' => 1,
        ]);

        $upgrade = ServiceType::create([
            'name' => 'Upgrade',
            'slug' => 'upgrade',
            'color' => '#3b82f6',
            'icon' => '⬆️',
            'status' => 'active',
            'order' => 2,
        ]);

        $maintenance = ServiceType::create([
            'name' => 'Maintenance',
            'slug' => 'maintenance',
            'color' => '#10b981',
            'icon' => '⚙️',
            'status' => 'active',
            'order' => 3,
        ]);

        $installation = ServiceType::create([
            'name' => 'Installation',
            'slug' => 'installation',
            'color' => '#f59e0b',
            'icon' => '🛠️',
            'status' => 'active',
            'order' => 4,
        ]);

        // Create Categories
        $laptopCategory = Category::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Professional laptop repair, upgrade, and maintenance services',
            'icon' => '💻',
            'status' => 'active',
            'order' => 1,
        ]);

        $printerCategory = Category::create([
            'name' => 'Printer',
            'slug' => 'printer',
            'description' => 'Complete printer repair and maintenance solutions',
            'icon' => '🖨️',
            'status' => 'active',
            'order' => 2,
        ]);

        $washingMachineCategory = Category::create([
            'name' => 'Washing Machine',
            'slug' => 'washing-machine',
            'description' => 'Expert washing machine repair and maintenance',
            'icon' => '🧺',
            'status' => 'active',
            'order' => 3,
        ]);

        $cellphoneCategory = Category::create([
            'name' => 'Cellphone',
            'slug' => 'cellphone',
            'description' => 'Professional smartphone repair with quality parts',
            'icon' => '📱',
            'status' => 'active',
            'order' => 4,
        ]);

        $cctvCategory = Category::create([
            'name' => 'CCTV',
            'slug' => 'cctv',
            'description' => 'Complete security camera system installation and maintenance',
            'icon' => '📹',
            'status' => 'active',
            'order' => 5,
        ]);

        $solarPanelCategory = Category::create([
            'name' => 'Solar Panel',
            'slug' => 'solar-panel',
            'description' => 'Residential and commercial solar power solutions',
            'icon' => '☀️',
            'status' => 'active',
            'order' => 6,
        ]);

        $clinicEquipmentCategory = Category::create([
            'name' => 'Clinic Equipment',
            'slug' => 'clinic-equipment',
            'description' => 'Professional calibration and maintenance of medical devices',
            'icon' => '🏥',
            'status' => 'active',
            'order' => 7,
        ]);

        // ==================== 1. LAPTOP SERVICE ====================
        $laptopService = Service::create([
            'category_id' => $laptopCategory->id,
            'name' => 'Laptop Service',
            'slug' => 'laptop-service',
            'subtitle' => 'LAPTOP',
            'description' => 'Professional laptop repair, upgrade, and maintenance services.',
            'long_description' => 'Our expert technicians provide comprehensive laptop services including motherboard repair, component upgrades, and system optimization. We handle all major brands and models with guaranteed quality service.',
            'icon' => '💻',
            'status' => 'active',
            'featured' => true,
            'order' => 1,
        ]);

        $laptopService->serviceTypes()->attach([$repair->id, $upgrade->id]);

        // Repair scope of work
        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $repair->id,
            'description' => 'Motherboard diagnostics and repair',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $repair->id,
            'description' => 'Component replacement',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $repair->id,
            'description' => 'Thermal paste application',
            'order' => 3,
            'status' => 'active',
        ]);

        // Upgrade scope of work
        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $upgrade->id,
            'description' => 'SSD installation and data migration',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $upgrade->id,
            'description' => 'RAM upgrade',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $laptopService->id,
            'service_type_id' => $upgrade->id,
            'description' => 'BIOS update',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 2. PRINTER SERVICE ====================
        $printerService = Service::create([
            'category_id' => $printerCategory->id,
            'name' => 'Printer Service',
            'slug' => 'printer-service',
            'subtitle' => 'PRINTER',
            'description' => 'Complete printer repair and maintenance solutions.',
            'long_description' => 'Professional printer servicing for all types of printers including inkjet, laser, and multifunction devices. We handle hardware repairs, maintenance, and quality optimization.',
            'icon' => '🖨️',
            'status' => 'active',
            'featured' => true,
            'order' => 2,
        ]);

        $printerService->serviceTypes()->attach([$repair->id, $maintenance->id]);

        // Repair scope of work
        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $repair->id,
            'description' => 'Paper jam fixes',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $repair->id,
            'description' => 'Roller replacement',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $repair->id,
            'description' => 'Print head cleaning',
            'order' => 3,
            'status' => 'active',
        ]);

        // Maintenance scope of work
        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Regular cleaning',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Firmware updates',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $printerService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Quality calibration',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 3. WASHING MACHINE SERVICE ====================
        $washingMachineService = Service::create([
            'category_id' => $washingMachineCategory->id,
            'name' => 'Washing Machine Service',
            'slug' => 'washing-machine-service',
            'subtitle' => 'WASHING MACHINE',
            'description' => 'Expert washing machine repair and maintenance.',
            'long_description' => 'Specialized washing machine repair services for all brands including top-load, front-load, and automatic machines. We handle mechanical and electronic issues with genuine parts.',
            'icon' => '🧺',
            'status' => 'active',
            'featured' => true,
            'order' => 3,
        ]);

        $washingMachineService->serviceTypes()->attach([$repair->id, $maintenance->id]);

        // Repair scope of work
        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $repair->id,
            'description' => 'Drain pump replacement',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $repair->id,
            'description' => 'Door lock repair',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $repair->id,
            'description' => 'Water valve service',
            'order' => 3,
            'status' => 'active',
        ]);

        // Maintenance scope of work
        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Deep cleaning',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Filter maintenance',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $washingMachineService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Balance adjustment',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 4. CELLPHONE SERVICE ====================
        $cellphoneService = Service::create([
            'category_id' => $cellphoneCategory->id,
            'name' => 'Cellphone Service',
            'slug' => 'cellphone-service',
            'subtitle' => 'CELLPHONE',
            'description' => 'Professional smartphone repair with quality parts.',
            'long_description' => 'Expert smartphone repair services for all major brands including screen replacement, battery service, and water damage recovery. We use high-quality replacement parts with warranty.',
            'icon' => '📱',
            'status' => 'active',
            'featured' => true,
            'order' => 4,
        ]);

        $cellphoneService->serviceTypes()->attach([$repair->id]);

        ScopeOfWork::create([
            'service_id' => $cellphoneService->id,
            'service_type_id' => $repair->id,
            'description' => 'Screen replacement',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cellphoneService->id,
            'service_type_id' => $repair->id,
            'description' => 'Battery replacement',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cellphoneService->id,
            'service_type_id' => $repair->id,
            'description' => 'Water damage repair',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 5. CCTV INSTALLATION ====================
        $cctvService = Service::create([
            'category_id' => $cctvCategory->id,
            'name' => 'CCTV Installation',
            'slug' => 'cctv-installation',
            'subtitle' => 'CCTV',
            'description' => 'Complete security camera system installation and maintenance.',
            'long_description' => 'Professional CCTV installation services including IP cameras, NVR systems, and remote monitoring setup. We provide comprehensive security solutions for homes and businesses.',
            'icon' => '📹',
            'status' => 'active',
            'featured' => true,
            'order' => 5,
        ]);

        $cctvService->serviceTypes()->attach([$installation->id, $maintenance->id]);

        // Installation scope of work
        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $installation->id,
            'description' => 'Camera installation at strategic locations',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $installation->id,
            'description' => 'NVR setup and configuration',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $installation->id,
            'description' => 'Remote viewing setup',
            'order' => 3,
            'status' => 'active',
        ]);

        // Maintenance scope of work
        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'System monitoring',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Firmware updates',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $cctvService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Cable inspection',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 6. SOLAR PANEL INSTALLATION ====================
        $solarPanelService = Service::create([
            'category_id' => $solarPanelCategory->id,
            'name' => 'Solar Panel Installation',
            'slug' => 'solar-panel-installation',
            'subtitle' => 'SOLAR PANEL',
            'description' => 'Residential and commercial solar power solutions.',
            'long_description' => 'Complete solar panel installation services including system design, panel installation, inverter setup, and grid connection. We provide sustainable energy solutions with long-term maintenance support.',
            'icon' => '☀️',
            'status' => 'active',
            'featured' => true,
            'order' => 6,
        ]);

        $solarPanelService->serviceTypes()->attach([$installation->id, $maintenance->id]);

        // Installation scope of work
        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $installation->id,
            'description' => 'Panel array installation',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $installation->id,
            'description' => 'Inverter setup',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $installation->id,
            'description' => 'Grid connection',
            'order' => 3,
            'status' => 'active',
        ]);

        // Maintenance scope of work
        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Panel cleaning',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Performance monitoring',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $solarPanelService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Connection inspection',
            'order' => 3,
            'status' => 'active',
        ]);

        // ==================== 7. MEDICAL EQUIPMENT SERVICE ====================
        $clinicEquipmentService = Service::create([
            'category_id' => $clinicEquipmentCategory->id,
            'name' => 'Medical Equipment Service',
            'slug' => 'medical-equipment-service',
            'subtitle' => 'CLINIC EQUIPMENT',
            'description' => 'Professional calibration and maintenance of medical devices.',
            'long_description' => 'Specialized medical equipment servicing including calibration, safety compliance checks, and preventive maintenance for various medical devices used in clinics and healthcare facilities.',
            'icon' => '🏥',
            'status' => 'active',
            'featured' => false,
            'order' => 7,
        ]);

        $clinicEquipmentService->serviceTypes()->attach([$maintenance->id]);

        ScopeOfWork::create([
            'service_id' => $clinicEquipmentService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Equipment calibration',
            'order' => 1,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $clinicEquipmentService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Safety compliance check',
            'order' => 2,
            'status' => 'active',
        ]);

        ScopeOfWork::create([
            'service_id' => $clinicEquipmentService->id,
            'service_type_id' => $maintenance->id,
            'description' => 'Sterilization and cleaning',
            'order' => 3,
            'status' => 'active',
        ]);
    }
}