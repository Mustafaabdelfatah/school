<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create main "About University" page
        $aboutPage = Page::create([
            'title' => 'عن الجامعة',
            'slug' => 'about-university',
            'content' => 'تُعتبر جامعة دمشق المؤسسة التعليمية الرئيسية للتعليم العالي في سوريا وذلك منذ تأسيسها عام 1923، حيث تقف اليوم كمنارةٍ للتميُّز الأكاديمي والبحث العلمي في المنطقة العربية.',
            'meta_description' => 'معلومات شاملة عن جامعة دمشق، تاريخها، رسالتها، ورؤيتها',
            'meta_keywords' => 'جامعة دمشق، التعليم العالي، سوريا، الأكاديمي',
            'sort_order' => 1,
            'is_active' => true,
            'show_in_menu' => true,
            'menu_icon' => 'fas fa-university',
            'template' => 'about'
        ]);

        // Create sections for About University page
        PageSection::create([
            'page_id' => $aboutPage->id,
            'type' => 'hero',
            'title' => 'مرحباً بكم في جامعة دمشق',
            'content' => 'منارة العلم والمعرفة منذ عام 1923',
            'sort_order' => 1,
            'is_active' => true
        ]);

        PageSection::create([
            'page_id' => $aboutPage->id,
            'type' => 'text',
            'title' => 'رسالة الجامعة',
            'content' => 'تلتزم الجامعة بتقديم تعليمٍ عالي الجودة في مختلف المجالات العلمية ذات الأولوية الوطنية.',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Create sub-pages for About University
        $administrationPage = Page::create([
            'title' => 'إدارة الجامعة',
            'slug' => 'administration',
            'content' => 'معلومات عن إدارة الجامعة ورئيس الجامعة ونواب الرئيس',
            'meta_description' => 'تعرف على إدارة جامعة دمشق والهيكل التنظيمي',
            'parent_id' => $aboutPage->id,
            'sort_order' => 1,
            'is_active' => true,
            'show_in_menu' => true,
            'template' => 'default'
        ]);

        // Create sections for Administration page
        PageSection::create([
            'page_id' => $administrationPage->id,
            'type' => 'text',
            'title' => 'رئيس الجامعة',
            'content' => 'الأستاذ الدكتور [اسم رئيس الجامعة] - معلومات عن رئيس الجامعة الحالي ومسيرته الأكاديمية والمهنية.',
            'sort_order' => 1,
            'is_active' => true
        ]);

        PageSection::create([
            'page_id' => $administrationPage->id,
            'type' => 'text',
            'title' => 'نواب رئيس الجامعة',
            'content' => 'نائب رئيس الجامعة للشؤون الأكاديمية، نائب رئيس الجامعة للشؤون الإدارية، نائب رئيس الجامعة للبحث العلمي.',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Create News page
        $newsPage = Page::create([
            'title' => 'الأخبار',
            'slug' => 'news',
            'content' => 'آخر أخبار وفعاليات جامعة دمشق',
            'meta_description' => 'تابع آخر أخبار وأحداث جامعة دمشق',
            'meta_keywords' => 'أخبار الجامعة، فعاليات، إعلانات',
            'sort_order' => 2,
            'is_active' => true,
            'show_in_menu' => true,
            'menu_icon' => 'fas fa-newspaper',
            'template' => 'news'
        ]);

        // Create Academic Programs page
        $programsPage = Page::create([
            'title' => 'البرامج الأكاديمية',
            'slug' => 'academic-programs',
            'content' => 'تقدِّم الجامعة خياراتٍ واسعة من البرامج الأكاديمية',
            'meta_description' => 'تعرف على البرامج الأكاديمية المتاحة في جامعة دمشق',
            'meta_keywords' => 'البرامج الأكاديمية، الكليات، التخصصات',
            'sort_order' => 3,
            'is_active' => true,
            'show_in_menu' => true,
            'menu_icon' => 'fas fa-graduation-cap',
            'template' => 'programs'
        ]);

        // Create sub-pages for Academic Programs
        Page::create([
            'title' => 'كلية الهندسة',
            'slug' => 'engineering',
            'content' => 'معلومات عن كلية الهندسة وأقسامها',
            'meta_description' => 'كلية الهندسة في جامعة دمشق',
            'parent_id' => $programsPage->id,
            'sort_order' => 1,
            'is_active' => true,
            'show_in_menu' => true
        ]);

        Page::create([
            'title' => 'كلية الطب البشري',
            'slug' => 'medicine',
            'content' => 'معلومات عن كلية الطب البشري ومناهجها',
            'meta_description' => 'كلية الطب البشري في جامعة دمشق',
            'parent_id' => $programsPage->id,
            'sort_order' => 2,
            'is_active' => true,
            'show_in_menu' => true
        ]);

        // Create Admission page
        Page::create([
            'title' => 'القبول والتسجيل',
            'slug' => 'admission',
            'content' => 'معلومات عن القبول والتسجيل في الجامعة',
            'meta_description' => 'شروط القبول والتسجيل في جامعة دمشق',
            'meta_keywords' => 'القبول، التسجيل، شروط القبول',
            'sort_order' => 4,
            'is_active' => true,
            'show_in_menu' => true,
            'menu_icon' => 'fas fa-user-graduate',
            'template' => 'default'
        ]);

        // Create Contact page
        Page::create([
            'title' => 'اتصل بنا',
            'slug' => 'contact',
            'content' => 'للتواصل مع جامعة دمشق',
            'meta_description' => 'معلومات الاتصال بجامعة دمشق',
            'meta_keywords' => 'اتصل بنا، التواصل، معلومات الاتصال',
            'sort_order' => 5,
            'is_active' => true,
            'show_in_menu' => true,
            'menu_icon' => 'fas fa-envelope',
            'template' => 'contact'
        ]);
    }
}
