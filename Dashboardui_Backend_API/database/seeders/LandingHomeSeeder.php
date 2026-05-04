<?php

namespace Database\Seeders;

use App\Models\LandingEntity;
use App\Models\LandingNavItem;
use App\Models\LandingPage;
use App\Models\LandingPageLocale;
use App\Models\LandingSection;
use Illuminate\Database\Seeder;

class LandingHomeSeeder extends Seeder
{
    /** Unique strings used by Feature tests (published JSON, not DB on GET). */
    public const HERO_HEADLINE_EN = 'LP_SEEDED_HERO_EN';

    public const HERO_HEADLINE_FR = 'LP_SEEDED_HERO_FR';

    public function run(): void
    {
        $page = LandingPage::query()->firstOrCreate(['slug' => 'home']);

        foreach (['en', 'fr', 'ar'] as $locale) {
            LandingPageLocale::query()->updateOrCreate(
                ['landing_page_id' => $page->id, 'locale' => $locale],
                [
                    'meta_title' => $locale === 'en' ? 'G-Appointement — Home' : null,
                    'meta_description' => $locale === 'en' ? 'Book appointments online.' : null,
                ]
            );
        }

        $sections = [
            ['key' => 'top_bar', 'order' => 0, 'settings' => null, 'content' => [
                'en' => ['phone' => '+1 555 0100', 'emergency' => '24/7 emergency line', 'hours' => 'Mon–Fri 8:00–18:00'],
                'fr' => ['phone' => '+33 1 23 45 67 89', 'emergency' => 'Urgences 24h/24', 'hours' => 'Lun–Ven 8h–18h'],
                'ar' => ['phone' => '+966 11 000 0000', 'emergency' => 'طوارئ ٢٤/٧', 'hours' => 'الأحد–الخميس ٨–١٨'],
            ]],
            ['key' => 'hero', 'order' => 1, 'settings' => null, 'content' => [
                'en' => [
                    'tagline' => 'Care, simplified',
                    'headline' => self::HERO_HEADLINE_EN,
                    'subhead' => 'Scheduling that fits your clinic and your patients.',
                    'cta_primary' => 'See how it works',
                    'cta_primary_href' => '#features',
                    'cta_secondary' => 'About us',
                    'cta_secondary_href' => '#about',
                ],
                'fr' => [
                    'tagline' => 'Soins, simplifiés',
                    'headline' => self::HERO_HEADLINE_FR,
                    'subhead' => 'Une prise de rendez-vous qui respecte votre cabinet et vos patients.',
                    'cta_primary' => 'Découvrir',
                    'cta_primary_href' => '#features',
                    'cta_secondary' => 'À propos',
                    'cta_secondary_href' => '#about',
                ],
                'ar' => [
                    'tagline' => 'رعاية مبسّطة',
                    'headline' => 'جدولة مواعيد بسهولة',
                    'subhead' => 'نظام يناسب عيادتك ومرضاك.',
                    'cta_primary' => 'اكتشف المزيد',
                    'cta_primary_href' => '#features',
                    'cta_secondary' => 'من نحن',
                    'cta_secondary_href' => '#about',
                ],
            ]],
            ['key' => 'about', 'order' => 2, 'settings' => null, 'content' => [
                'en' => ['title' => 'About G-Appointement', 'lead' => 'Built for modern practices.', 'body' => 'We help clinics reduce no-shows and give patients a clear path to care.'],
                'fr' => ['title' => 'À propos', 'lead' => 'Pensé pour les cabinets modernes.', 'body' => 'Réduisez les absences et simplifiez le parcours patient.'],
                'ar' => ['title' => 'عن المنصة', 'lead' => 'للعيادات الحديثة.', 'body' => 'نساعدك على تقليل الغياب وتسهيل حجز المواعيد.'],
            ]],
            ['key' => 'departments', 'order' => 3, 'settings' => null, 'content' => []],
            ['key' => 'featured_doctors', 'order' => 4, 'settings' => null, 'content' => []],
            ['key' => 'quick_booking', 'order' => 5, 'settings' => ['enabled' => true], 'content' => [
                'en' => ['title' => 'Quick booking', 'hint' => 'Choose a department to get started.'],
                'fr' => ['title' => 'Réservation rapide', 'hint' => 'Choisissez un service.'],
                'ar' => ['title' => 'حجز سريع', 'hint' => 'اختر القسم للبدء.'],
            ]],
            ['key' => 'why_us', 'order' => 6, 'settings' => null, 'content' => [
                'en' => ['title' => 'Why clinics choose us', 'subtitle' => 'Practical tools for front desk and clinicians.'],
                'fr' => ['title' => 'Pourquoi nous choisir', 'subtitle' => 'Des outils utiles au quotidien.'],
                'ar' => ['title' => 'لماذا نحن', 'subtitle' => 'أدوات عملية للاستقبال والأطباء.'],
            ]],
            ['key' => 'testimonials', 'order' => 7, 'settings' => null, 'content' => []],
            ['key' => 'blog', 'order' => 8, 'settings' => null, 'content' => []],
            ['key' => 'contact', 'order' => 9, 'settings' => null, 'content' => [
                'en' => ['title' => 'Contact', 'body' => 'Reach the team for demos and support.', 'cta' => 'Email us'],
                'fr' => ['title' => 'Contact', 'body' => 'Démos et support.', 'cta' => 'Écrivez-nous'],
                'ar' => ['title' => 'اتصل بنا', 'body' => 'للعروض والدعم.', 'cta' => 'راسلنا'],
            ]],
            ['key' => 'cta', 'order' => 10, 'settings' => null, 'content' => [
                'en' => ['title' => 'Ready to streamline scheduling?', 'body' => 'Sign in to your clinic dashboard.', 'button' => 'Admin login'],
                'fr' => ['title' => 'Prêt à simplifier les rendez-vous ?', 'body' => 'Connectez-vous au tableau de bord.', 'button' => 'Connexion admin'],
                'ar' => ['title' => 'جاهز لتبسيط المواعيد؟', 'body' => 'سجّل الدخول إلى لوحة العيادة.', 'button' => 'دخول المشرف'],
            ]],
            ['key' => 'footer', 'order' => 11, 'settings' => null, 'content' => [
                'en' => ['line' => '© G-Appointement. All rights reserved.'],
                'fr' => ['line' => '© G-Appointement. Tous droits réservés.'],
                'ar' => ['line' => '© G-Appointement. جميع الحقوق محفوظة.'],
            ]],
        ];

        foreach ($sections as $def) {
            $section = LandingSection::query()->updateOrCreate(
                ['landing_page_id' => $page->id, 'section_key' => $def['key']],
                ['sort_order' => $def['order'], 'settings' => $def['settings']]
            );
            foreach ($def['content'] as $locale => $content) {
                if ($content === []) {
                    continue;
                }
                $section->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['content' => $content]
                );
            }
        }

        $navDefs = [
            ['order' => 0, 'href' => '#about', 'route_key' => null, 'is_cta' => false, 'labels' => ['en' => 'About', 'fr' => 'À propos', 'ar' => 'من نحن']],
            ['order' => 1, 'href' => '#features', 'route_key' => null, 'is_cta' => false, 'labels' => ['en' => 'Features', 'fr' => 'Fonctions', 'ar' => 'المزايا']],
            ['order' => 2, 'href' => '#contact', 'route_key' => null, 'is_cta' => false, 'labels' => ['en' => 'Contact', 'fr' => 'Contact', 'ar' => 'اتصل']],
            ['order' => 3, 'href' => '#appointments', 'route_key' => null, 'is_cta' => true, 'labels' => ['en' => 'Book appointment', 'fr' => 'Prendre rendez-vous', 'ar' => 'احجز موعداً']],
        ];

        LandingNavItem::query()->where('landing_page_id', $page->id)->delete();

        foreach ($navDefs as $nd) {
            $nav = LandingNavItem::query()->create([
                'landing_page_id' => $page->id,
                'sort_order' => $nd['order'],
                'href' => $nd['href'],
                'route_key' => $nd['route_key'],
                'is_visible' => true,
                'is_cta' => $nd['is_cta'],
                'icon' => null,
            ]);
            foreach (['en', 'fr', 'ar'] as $loc) {
                $nav->translations()->create([
                    'locale' => $loc,
                    'label' => $nd['labels'][$loc] ?? $nd['labels']['en'],
                ]);
            }
        }

        $deptSection = LandingSection::query()->where('landing_page_id', $page->id)->where('section_key', 'departments')->first();
        if ($deptSection) {
            LandingEntity::query()->where('landing_section_id', $deptSection->id)->delete();
            $d1 = LandingEntity::query()->create([
                'landing_section_id' => $deptSection->id,
                'type' => 'department',
                'sort_order' => 0,
                'slug' => 'cardiology',
                'image_path' => null,
                'href' => '#departments',
                'user_id' => null,
                'extra' => null,
            ]);
            foreach (['en' => ['title' => 'Cardiology', 'subtitle' => 'Heart health'], 'fr' => ['title' => 'Cardiologie', 'subtitle' => 'Santé cardiaque'], 'ar' => ['title' => 'أمراض القلب', 'subtitle' => 'صحة القلب']] as $loc => $t) {
                $d1->translations()->create(array_merge(['locale' => $loc], $t, ['body' => null, 'cta_label' => null]));
            }
        }

        $docSection = LandingSection::query()->where('landing_page_id', $page->id)->where('section_key', 'featured_doctors')->first();
        if ($docSection) {
            LandingEntity::query()->where('landing_section_id', $docSection->id)->delete();
            $doc = LandingEntity::query()->create([
                'landing_section_id' => $docSection->id,
                'type' => 'doctor',
                'sort_order' => 0,
                'slug' => 'dr-demo',
                'image_path' => null,
                'href' => '#doctors',
                'user_id' => null,
                'extra' => null,
            ]);
            foreach (['en' => ['title' => 'Dr. Demo', 'subtitle' => 'General practice'], 'fr' => ['title' => 'Dr Demo', 'subtitle' => 'Médecine générale'], 'ar' => ['title' => 'د. تجريبي', 'subtitle' => 'طب عام']] as $loc => $t) {
                $doc->translations()->create(array_merge(['locale' => $loc], $t, ['body' => null, 'cta_label' => 'Book']));
            }
        }

        $whySection = LandingSection::query()->where('landing_page_id', $page->id)->where('section_key', 'why_us')->first();
        if ($whySection) {
            LandingEntity::query()->where('landing_section_id', $whySection->id)->delete();
            $cards = [
                ['order' => 0, 'en' => ['title' => 'Fewer no-shows', 'body' => 'Smart reminders keep patients on track.'], 'fr' => ['title' => "Moins d'absences", 'body' => 'Rappels automatiques.'], 'ar' => ['title' => 'تقليل الغياب', 'body' => 'تذكيرات ذكية.']],
                ['order' => 1, 'en' => ['title' => 'Clear waitlists', 'body' => 'Fill openings when slots free up.'], 'fr' => ['title' => 'Listes d’attente', 'body' => 'Remplissez les créneaux libérés.'], 'ar' => ['title' => 'قوائم انتظار', 'body' => 'املأ الأوقات الفارغة.']],
                ['order' => 2, 'en' => ['title' => 'Secure by design', 'body' => 'Role-based access for your team.'], 'fr' => ['title' => 'Sécurité', 'body' => 'Accès par rôles.'], 'ar' => ['title' => 'أمان', 'body' => 'صلاحيات حسب الدور.']],
            ];
            foreach ($cards as $c) {
                $e = LandingEntity::query()->create([
                    'landing_section_id' => $whySection->id,
                    'type' => 'feature',
                    'sort_order' => $c['order'],
                    'slug' => null,
                    'image_path' => null,
                    'href' => null,
                    'user_id' => null,
                    'extra' => null,
                ]);
                foreach (['en', 'fr', 'ar'] as $loc) {
                    $t = $c[$loc];
                    $e->translations()->create([
                        'locale' => $loc,
                        'title' => $t['title'],
                        'subtitle' => null,
                        'body' => $t['body'],
                        'cta_label' => null,
                    ]);
                }
            }
        }

        $testSection = LandingSection::query()->where('landing_page_id', $page->id)->where('section_key', 'testimonials')->first();
        if ($testSection) {
            LandingEntity::query()->where('landing_section_id', $testSection->id)->delete();
            $t1 = LandingEntity::query()->create([
                'landing_section_id' => $testSection->id,
                'type' => 'testimonial',
                'sort_order' => 0,
                'slug' => null,
                'image_path' => null,
                'href' => null,
                'user_id' => null,
                'extra' => null,
            ]);
            foreach (['en' => ['title' => 'Clinic lead', 'body' => '“Our front desk finally has one place to manage bookings.”'], 'fr' => ['title' => 'Responsable cabinet', 'body' => '« Enfin un seul endroit pour les rendez-vous. »'], 'ar' => ['title' => 'مسؤول عيادة', 'body' => '« مكان واحد لإدارة المواعيد. »']] as $loc => $t) {
                $t1->translations()->create([
                    'locale' => $loc,
                    'title' => $t['title'],
                    'subtitle' => null,
                    'body' => $t['body'],
                    'cta_label' => null,
                ]);
            }
        }

        $blogSection = LandingSection::query()->where('landing_page_id', $page->id)->where('section_key', 'blog')->first();
        if ($blogSection) {
            LandingEntity::query()->where('landing_section_id', $blogSection->id)->delete();
            $b1 = LandingEntity::query()->create([
                'landing_section_id' => $blogSection->id,
                'type' => 'blog_teaser',
                'sort_order' => 0,
                'slug' => 'welcome-post',
                'image_path' => null,
                'href' => '#blog',
                'user_id' => null,
                'extra' => null,
            ]);
            foreach (['en' => ['title' => 'What’s new', 'body' => 'Product updates and tips for clinics.', 'cta_label' => 'Read'], 'fr' => ['title' => 'Nouveautés', 'body' => 'Mises à jour produit.', 'cta_label' => 'Lire'], 'ar' => ['title' => 'جديد', 'body' => 'تحديثات المنتج.', 'cta_label' => 'اقرأ']] as $loc => $t) {
                $b1->translations()->create(array_merge(['locale' => $loc], $t, ['subtitle' => null]));
            }
        }
    }
}
