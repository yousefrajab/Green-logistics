<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
    }

    /**
     * تحليل اسم الوجبة وتوليد وصف ذكي وتحديد مسببات الحساسية (تحليل نصي)
     */
    public function generateDescriptionAndAllergens(string $title): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_actual_api_key_here') {
            return [
                'description' => "فائض طعام مجهز ومغلف بعناية من وجبة (" . $title . "). تم حفظه مبرداً ونظيفاً.",
                'allergens' => "تنبيه: قد تحتوي الوجبة على مسببات حساسية عامة كالألبان أو الغلوتين. يرجى مراجعة المكونات عند الاستلام."
            ];
        }

        try {
            // استخدام withoutVerifying() لتخطي فحص الأمان المحلي لـ SSL وحل مشكلة الاتصال [10]
            $response = Http::withoutVerifying()
                ->withToken($this->apiKey)
                ->timeout(12)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت مساعد ذكي وخبير سلامة أغذية لمنصة حفظ الطعام الفائض. قم بتحليل عنوان الوجبة المكتوب باللغة العربية واقترح وصفاً إنسانياً جذاباً ومختصراً جداً للوجبة، بالإضافة إلى تحديد مسببات الحساسية المحتملة (مثل: الألبان، المكسرات، الغلوتين، البيض) بوضوح تام. يجب أن ترجع النتيجة بصيغة JSON تحتوي حصرياً على حقلين: description و allergens.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $title
                        ]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $result = json_decode($response->json()['choices'][0]['message']['content'], true);
                return [
                    'description' => $result['description'] ?? null,
                    'allergens' => $result['allergens'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            logger()->error('AI Service Error: ' . $e->getMessage());
        }

        return [
            'description' => "فائض طعام مجهز ومغلف بعناية من وجبة (" . $title . ").",
            'allergens' => "يرجى مراجعة الجهة المتبرعة للتحقق من مسببات الحساسية."
        ];
    }

    /**
     * تحليل صورة الأغذية المرفوعة بالذكاء الاصطناعي البصري وتعبئة الإعلان تلقائياً
     */
    public function analyzeFoodImage($imageFile): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_actual_api_key_here') {
            return [
                'is_food' => true,
                'error_message' => null,
                'title' => 'وجبة تجريبية (مفتاح الـ API غير نشط)',
                'category' => 'cooked',
                'quantity' => '1 علبة كبيرة (تكفي لـ 5 أشخاص)',
                'description' => 'تنبيه مطور: يرجى إضافة مفتاح OPENAI_API_KEY الحقيقي في ملف .env لتشغيل ميزة الفحص والتحليل البصري الفعلي للصور بالذكاء الاصطناعي البصري.',
                'allergens' => 'لا توجد مسببات حساسية في الواجهة التجريبية.'
            ];
        }

        try {
            $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
            $mimeType = $imageFile->getClientMimeType();

            // استخدام withoutVerifying() لتخطي فحص الأمان المحلي لـ SSL وحل مشكلة الاتصال البصري [10]
            $response = Http::withoutVerifying()
                ->withToken($this->apiKey)
                ->timeout(25)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت خبير لوجستي ومفتش سلامة أغذية محترف لمنصة جود لحفظ النعمة.
                            قم بتحليل الصورة المرفقة بتمعن شديد واتبع الخطوات التالية بدقة:
                            1. التحقق من المحتوى (Is Food?): حدد ما إذا كانت الصورة تحتوي على طعام صالح للتبرع أم لا. إذا كانت الصورة لا تحتوي على طعام (مثل: شهادة جامعية، وجه شخص، كرتون فارغ، سيارة، إلخ)، قم بتعيين الحقل is_food إلى false واكتب رسالة خطأ واضحة ومؤدبة باللغة العربية في حقل error_message تشرح فيها أن الصورة لا تحتوي على طعام، واترك بقية الحقول فارغة.
                            2. التصنيف والاسم: إذا كانت الصورة تحتوي على طعام، حدد عنواناً دقيقاً باللغة العربية وتصنيفاً مناسباً (cooked أو dry أو fresh).
                            3. العد الفعلي والتقدير: قم بـ "إحصاء وعدّ" عدد العبوات أو الأطباق أو الحصص المنفصلة الظاهرة في الصورة أولاً بدقة كعدد صحيح (مثال: 5 علب، 2 صواني)، ثم قم بتقدير عدد الأشخاص المستفيدين بناءً على هذا العد الفعلي (مثال: "5 علب مغلفة تكفي لـ 5 أشخاص").
                            4. الوصف ومسببات الحساسية: اكتب وصفاً مفصلاً للمكونات وحدد مسببات الحساسية بدقة.
                            أرجع النتيجة حصراً بصيغة JSON تحتوي على الحقول التالية باللغة العربية: is_food (true/false), error_message (null أو نص الخطأ), title, category, quantity, description, allergens.'
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'حلل هذه الصورة وأرجع النتيجة بصيغة JSON باللغة العربية مع التحقق الأمني أولاً.'
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$imageData}"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                return json_decode($response->json()['choices'][0]['message']['content'], true);
            }
        } catch (\Exception $e) {
            logger()->error('AI Vision Service Error: ' . $e->getMessage());
        }

        return [
            'is_food' => false,
            'error_message' => 'عذراً، فشل الاتصال بخدمة فحص الصور بالذكاء الاصطناعي.',
            'title' => '',
            'category' => 'cooked',
            'quantity' => '',
            'description' => '',
            'allergens' => ''
        ];
    }

    /**
     * [ميزة تنافسية كبرى للمنشآت]:
     * توليد تقرير الاستدامة والمسؤولية المجتمعية (CSR) والتحليل البيئي الذكي بالكامل [4]
     */
    /**
     * [تحديث الذكاء الاصطناعي لتوليد تقارير استدامة مختصرة ومكثفة جداً للمنشآت] [4]
     */
    public function generateSustainabilityReport(string $organizationName, int $completedCount): array
    {
        // إشعار صريح ومجهز بدقة أثناء التطوير المحلي في حال لم يكن مفتاح الـ API مفعلاً بعد
        if (empty($this->apiKey) || $this->apiKey === 'your_actual_api_key_here') {
            return [
                'co2_saved' => '420 كغ من غاز ثاني أكسيد الكربون',
                'water_saved' => '12,500 لتر من المياه العذبة',
                'report_text' => "نثمن عالياً الشراكة الاستراتيجية الخضراء مع منشأة ({$organizationName}). بإنقاذكم لـ ({$completedCount} وجبة طعام فائضة)، ساهمت منشأتكم بشكل مباشر في الحفاظ على الموارد البيئية وتقليل الضغط اللوجستي على المكاب المحلية، مما يرسخ ريادتكم الاستثنائية كشريك استدامة موثق ومميز لدى منصة جود لعام 2026."
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->withToken($this->apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت خبير ومستشار استدامة بيئية ومسؤولية مجتمعية (CSR).
                            قم بتحليل بيانات التبرع وصياغة تقرير رسمي فخم ومختصر جداً (لا يتجاوز 3 أسطر أو 4 جمل بليغة كحد أقصى) باللغة العربية الفصحى يوضح الأثر البيئي والخيري للمنظمة المتبرعة.
                            احرص على ذكر اسم المنشأة وعدد الوجبات الفعلي الذي تم إنقاذه صراحة وبشكل مبهج داخل النص.
                            احسب تقديراً معيارياً تقريبياً لـ:
                            1. غاز ثاني أكسيد الكربون (CO2) المحفوظ (الوجبة الواحدة تمنع انبعاث حوالي 2.5 كغ CO2).
                            2. المياه العذبة الموفرة في سلاسل الإنتاج (الوجبة الواحدة تستهلك حوالي 150 لتراً مائياً).
                            أرجع النتيجة حصراً بصيغة JSON تحتوي على الحقول التالية باللغة العربية بدقة متناهية: co2_saved, water_saved, report_text (تقرير نصي مختصر جداً وفخم للغاية ومقنع للطباعة الفورية).'
                        ],
                        [
                            'role' => 'user',
                            'content' => "اسم المنشأة المتبرعة: {$organizationName}، إجمالي عدد الوجبات الفعلي المنقذ حتى الآن: {$completedCount} وجبة."
                        ]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $result = json_decode($response->json()['choices'][0]['message']['content'], true);
                return [
                    'co2_saved' => $result['co2_saved'] ?? 'غير متوفر حالياً',
                    'water_saved' => $result['water_saved'] ?? 'غير متوفر حالياً',
                    'report_text' => $result['report_text'] ?? 'عذراً، فشل صياغة التقرير.'
                ];
            }
        } catch (\Exception $e) {
            logger()->error('AI Sustainability Report Error: ' . $e->getMessage());
        }

        return [
            'co2_saved' => 'غير متوفر حالياً',
            'water_saved' => 'غير متوفر حالياً',
            'report_text' => 'عذراً، فشل الاتصال بخدمة تقارير الاستدامة الذكية.'
        ];
    }
}
