<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected string $apiKey;

    public function __construct()
    {
        // جلب مفتاح الـ API من ملف الإعدادات
        $this->apiKey = config('services.openai.key', '');
    }

    /**
     * تحليل اسم الوجبة وتوليد وصف ذكي وتحديد مسببات الحساسية
     */
    public function generateDescriptionAndAllergens(string $title): array
    {
        // كود احتياطي (Fallback) في حال لم يتم إدخال مفتاح الـ API بعد أثناء التطوير المحلي
        if (empty($this->apiKey) || $this->apiKey === 'your_actual_api_key_here') {
            return [
                'description' => "فائض طعام مجهز ومغلف بعناية من وجبة (" . $title . "). تم حفظه مبرداً ونظيفاً.",
                'allergens' => "تنبيه: قد تحتوي الوجبة على مسببات حساسية عامة كالألبان أو الغلوتين. يرجى مراجعة المكونات عند الاستلام."
            ];
        }

        try {
            // استخدام عميل الـ HTTP المدمج في لارافيل للاتصال بـ OpenAI
            $response = Http::withToken($this->apiKey)
                ->timeout(10) // مهلة زمنية قصيرة لضمان عدم تعليق الخادم
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
                    'response_format' => ['type' => 'json_object'] // إجبار الموديل على إرجاع كائن JSON منظم
                ]);

            if ($response->successful()) {
                $result = json_decode($response->json()['choices'][0]['message']['content'], true);
                return [
                    'description' => $result['description'] ?? null,
                    'allergens' => $result['allergens'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات (Logs) والرجوع للبيانات الافتراضية بدلاً من تعطيل الموقع
            logger()->error('AI Service Error: ' . $e->getMessage());
        }

        return [
            'description' => "فائض طعام مجهز ومغلف بعناية من وجبة (" . $title . ").",
            'allergens' => "يرجى مراجعة الجهة المتبرعة للتحقق من مسببات الحساسية."
        ];
    }
}