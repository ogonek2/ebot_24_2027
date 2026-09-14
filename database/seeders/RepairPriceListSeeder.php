<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\RepairItem;
use App\Models\RepairPriceList;
use App\Models\RepairSection;
use Illuminate\Database\Seeder;

class RepairPriceListSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('href', 'remont-vzuttya')->first()
            ?? Category::where('name', 'like', '%Ремонт взуття%')->first();

        if (!$category) {
            $this->command?->warn('Category remont-vzuttya not found — skip RepairPriceListSeeder');
            return;
        }

        $list = RepairPriceList::query()->updateOrCreate(
            ['category_id' => $category->id],
            [
                'title' => 'Прайс на ремонт',
                'is_published' => true,
            ]
        );

        // Rebuild sections to keep seed idempotent
        $list->sections()->each(function (RepairSection $section) {
            $section->items()->delete();
            $section->delete();
        });

        $sections = $this->sectionsData();
        foreach ($sections as $si => $sectionData) {
            $section = RepairSection::create([
                'repair_price_list_id' => $list->id,
                'title' => $sectionData['title'],
                'sort_order' => $si,
            ]);
            foreach ($sectionData['items'] as $ii => $item) {
                RepairItem::create([
                    'repair_section_id' => $section->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'price_prefix' => $item['prefix'] ?? 'від',
                    'note' => $item['note'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'sort_order' => $ii,
                ]);
            }
        }
    }

    /** @return list<array{title:string,items:list<array<string,mixed>>}> */
    private function sectionsData(): array
    {
        return [
            [
                'title' => 'Набійки',
                'items' => [
                    ['name' => 'Жіночі штифтові', 'price' => 500],
                    ['name' => 'Жіночі (поліуретан/гума)', 'price' => 700],
                    ['name' => 'Чоловічі (поліуретан/гума)', 'price' => 800],
                    ['name' => 'Комбіновані набійки', 'price' => 950],
                    ['name' => 'Донарощування під набійку', 'price' => 300, 'prefix' => ''],
                    ['name' => 'Тракторні набійки', 'price' => 900],
                ],
            ],
            [
                'title' => 'Профілактика',
                'items' => [
                    ['name' => 'Профілактика Vibram', 'price' => 950],
                    ['name' => 'Профілактика повного сліду (до набійки)', 'price' => 1400],
                    ['name' => 'Профілактика повного сліду (спортивне взуття)', 'price' => 1500, 'prefix' => ''],
                    ['name' => 'Профілактика Casali Mirror (дзеркальна)', 'price' => 1700, 'prefix' => ''],
                    ['name' => 'Донарощування під профілактику', 'price' => 300],
                    ['name' => 'Повна переклейка підошви', 'price' => 1550],
                    ['name' => 'Підклейка підошви (до трьох місць)', 'price' => 500, 'prefix' => ''],
                    ['name' => 'Розтяжка взуття', 'price' => 600, 'note' => 'тільки шкіра або замш'],
                ],
            ],
            [
                'title' => 'Устілки',
                'items' => [
                    ['name' => 'Заміна вкладних устілок (шкіра)', 'price' => 950, 'prefix' => ''],
                    ['name' => 'Заміна устілок на відкритому взутті (носова частина)', 'price' => 900, 'prefix' => ''],
                    ['name' => 'Заміна основних устілок', 'price' => 1500],
                    ['name' => 'Заміна вкладних устілок (текстиль)', 'price' => 350, 'prefix' => ''],
                ],
            ],
            [
                'title' => 'Латки',
                'items' => [
                    ['name' => 'Встановлення внутрішньої латки', 'price' => 500, 'unit' => 'за одиницю'],
                    ['name' => 'Заміна деталей (декоративна латка)', 'price' => 800, 'unit' => 'за одиницю'],
                ],
            ],
            [
                'title' => 'Задники',
                'items' => [
                    ['name' => 'Заміна кишень задників (шкіра, замш, текстиль)', 'price' => 1500],
                    ['name' => 'Заміна жорстких задників', 'price' => 1500],
                ],
            ],
            [
                'title' => 'Фурнітура',
                'items' => [
                    ['name' => 'Полірування ранту', 'price' => 550],
                    ['name' => 'Заміна бігунка', 'price' => 550],
                    ['name' => 'Заміна блискавки', 'price' => 1500],
                ],
            ],
            [
                'title' => 'Відновлення швів',
                'items' => [
                    ['name' => 'Потайний шов (до 5 см)', 'price' => 600],
                    ['name' => 'Відновлення шва, ручна строчка (до 5 см)', 'price' => 550],
                    ['name' => 'Прошивка підошви по периметру', 'price' => 1250],
                ],
            ],
            [
                'title' => 'Підошва',
                'items' => [
                    ['name' => 'Заміна підошви (чепрак, кожвалон)', 'price' => 2000],
                    ['name' => 'Заміна формованої підошви з прошивкою (без вартості підошви)', 'price' => 2200, 'prefix' => ''],
                    ['name' => 'Заміна підошви та ранту Golden Goose', 'price' => 5500],
                ],
            ],
            [
                'title' => 'Перетяжка',
                'items' => [
                    ['name' => 'Перетяжка устілок (шкіра)', 'price' => 2400, 'prefix' => ''],
                    ['name' => 'Перетяжка підборів (простих)', 'price' => 1900],
                    ['name' => 'Перетяжка підборів (складних)', 'price' => 2500],
                    ['name' => 'Перетяжка шпальтом', 'price' => 2250],
                    ['name' => 'Заміна супінатора', 'price' => 1100, 'unit' => 'за один'],
                    ['name' => 'Укріплення підбору', 'price' => 700],
                    ['name' => 'Заміна резинок на літньому взутті', 'price' => 600],
                    ['name' => 'Пробив отвору на ремені', 'price' => 150, 'prefix' => '', 'unit' => 'за одиницю'],
                ],
            ],
        ];
    }
}
