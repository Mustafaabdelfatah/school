<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get pages for linking
        $aboutPage = Page::where('slug', 'about-university')->first();
        $newsPage = Page::where('slug', 'news')->first();
        $programsPage = Page::where('slug', 'academic-programs')->first();
        $admissionPage = Page::where('slug', 'admission')->first();
        $contactPage = Page::where('slug', 'contact')->first();

        // Create main header navigation items
        $aboutNav = Navigation::create([
            'name' => 'عن الجامعة',
            'page_id' => $aboutPage?->id,
            'icon' => 'fas fa-university',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'location' => 'header'
        ]);

        $newsNav = Navigation::create([
            'name' => 'الأخبار',
            'page_id' => $newsPage?->id,
            'icon' => 'fas fa-newspaper',
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'location' => 'header'
        ]);

        $programsNav = Navigation::create([
            'name' => 'البرامج الأكاديمية',
            'page_id' => $programsPage?->id,
            'icon' => 'fas fa-graduation-cap',
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'location' => 'header'
        ]);

        Navigation::create([
            'name' => 'القبول والتسجيل',
            'page_id' => $admissionPage?->id,
            'icon' => 'fas fa-user-graduate',
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'location' => 'header'
        ]);

        Navigation::create([
            'name' => 'اتصل بنا',
            'page_id' => $contactPage?->id,
            'icon' => 'fas fa-envelope',
            'target' => '_self',
            'sort_order' => 5,
            'is_active' => true,
            'location' => 'header'
        ]);

        // Create sub-navigation items for "About University"
        if ($aboutNav) {
            $administrationPage = Page::where('slug', 'administration')->where('parent_id', $aboutPage?->id)->first();

            Navigation::create([
                'name' => 'إدارة الجامعة',
                'parent_id' => $aboutNav->id,
                'url' => 'about-university/administration',
                'icon' => 'fas fa-users-cog',
                'target' => '_self',
                'sort_order' => 1,
                'is_active' => true,
                'location' => 'header'
            ]);

            Navigation::create([
                'name' => 'رسالة ترحيب رئيس الجامعة',
                'parent_id' => $aboutNav->id,
                'url' => '#',
                'icon' => 'fas fa-user-tie',
                'target' => '_self',
                'sort_order' => 2,
                'is_active' => true,
                'location' => 'header'
            ]);

            Navigation::create([
                'name' => 'الرسالة والرؤية',
                'parent_id' => $aboutNav->id,
                'url' => '#',
                'icon' => 'fas fa-eye',
                'target' => '_self',
                'sort_order' => 3,
                'is_active' => true,
                'location' => 'header'
            ]);

            Navigation::create([
                'name' => 'الهيكل التنظيمي',
                'parent_id' => $aboutNav->id,
                'url' => '#',
                'icon' => 'fas fa-sitemap',
                'target' => '_self',
                'sort_order' => 4,
                'is_active' => true,
                'location' => 'header'
            ]);
        }

        // Create sub-navigation items for "Academic Programs"
        if ($programsNav) {
            $engineeringPage = Page::where('slug', 'engineering')->where('parent_id', $programsPage?->id)->first();
            $medicinePage = Page::where('slug', 'medicine')->where('parent_id', $programsPage?->id)->first();

            if ($engineeringPage) {
                Navigation::create([
                    'name' => 'كلية الهندسة',
                    'parent_id' => $programsNav->id,
                    'page_id' => $engineeringPage->id,
                    'icon' => 'fas fa-cogs',
                    'target' => '_self',
                    'sort_order' => 1,
                    'is_active' => true,
                    'location' => 'header'
                ]);
            }

            if ($medicinePage) {
                Navigation::create([
                    'name' => 'كلية الطب البشري',
                    'parent_id' => $programsNav->id,
                    'page_id' => $medicinePage->id,
                    'icon' => 'fas fa-user-md',
                    'target' => '_self',
                    'sort_order' => 2,
                    'is_active' => true,
                    'location' => 'header'
                ]);
            }

            Navigation::create([
                'name' => 'كلية الآداب والعلوم الإنسانية',
                'parent_id' => $programsNav->id,
                'url' => '#',
                'icon' => 'fas fa-book',
                'target' => '_self',
                'sort_order' => 3,
                'is_active' => true,
                'location' => 'header'
            ]);
        }

        // Create footer navigation
        Navigation::create([
            'name' => 'الرئيسية',
            'url' => '/',
            'icon' => 'fas fa-home',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'location' => 'footer'
        ]);

        Navigation::create([
            'name' => 'سياسة الخصوصية',
            'url' => '/privacy-policy',
            'icon' => 'fas fa-shield-alt',
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'location' => 'footer'
        ]);

        Navigation::create([
            'name' => 'شروط الاستخدام',
            'url' => '/terms-of-use',
            'icon' => 'fas fa-file-contract',
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'location' => 'footer'
        ]);

        Navigation::create([
            'name' => 'خريطة الموقع',
            'url' => '/sitemap',
            'icon' => 'fas fa-map',
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'location' => 'footer'
        ]);

        // Create sidebar navigation for quick links
        Navigation::create([
            'name' => 'البحث العلمي',
            'url' => '/research',
            'icon' => 'fas fa-microscope',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'location' => 'sidebar'
        ]);

        Navigation::create([
            'name' => 'العلاقات الدولية',
            'url' => '/international-relations',
            'icon' => 'fas fa-globe',
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'location' => 'sidebar'
        ]);

        Navigation::create([
            'name' => 'التنمية المستدامة',
            'url' => '/sustainability',
            'icon' => 'fas fa-leaf',
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'location' => 'sidebar'
        ]);
    }
}
