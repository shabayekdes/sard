<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\CaseModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get company users
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            // Get task types and statuses for this company
            $taskTypes = TaskType::where('tenant_id', $companyUser->tenant_id)->get();
            $taskStatuses = TaskStatus::where('tenant_id', $companyUser->tenant_id)->get();
            $cases = CaseModel::where('tenant_id', $companyUser->tenant_id)
                ->whereNotNull('client_id')
                ->get();
            $users = User::where('tenant_id', $companyUser->tenant_id)->get();
            
            if ($taskTypes->count() > 0 && $taskStatuses->count() > 0) {
                // Create 2-3 tasks per company
                $taskCount = rand(8, 10);
                $taskTitles = [
                    'البحث عن السوابق القضائية في قضية العميل',
                    'صياغة طلب الحكم الملخص',
                    'اجتماع استشارة مع العميل',
                    'مراجعة تعديلات العقد',
                    'تقديم طلبات التقصي',
                    'إعداد مستندات التقديم للمحكمة',
                    'إجراء تحليل قانوني',
                    'جدولة جلسة الإفادة',
                ];

                $descriptions = [
                    'إجراء بحث قانوني شامل في القضايا المشابهة والسوابق القضائية',
                    'إعداد وصياغة الطلب استناداً إلى نتائج البحث',
                    'جدولة وعقد اجتماع مع العميل لمناقشة استراتيجية القضية',
                    'مراجعة وتحليل التعديلات المقترحة من محامي الطرف المقابل',
                    'إعداد وتقديم طلبات التقصي أمام المحكمة',
                    'إعداد مستندات التقديم والمرافعات اللازمة للمحكمة',
                    'إجراء تحليل قانوني تفصيلي لإعداد القضية',
                    'جدولة وتنسيق جلسة الإفادة مع جميع الأطراف',
                ];
                
                $priorities = ['low', 'medium', 'high', 'critical'];
                
                for ($i = 1; $i <= $taskCount; $i++) {
                    $dueDate = rand(1, 10) > 5 ? now()->addDays(rand(1, 30)) : now()->subDays(rand(1, 15));
                    $taskType = $taskTypes->random();
                    $taskStatus = $taskStatuses->random();
                    
                    $taskData = [
                        'task_id' => null, // Auto-generated
                        'title' => $taskTitles[($companyUser->id + $i - 1) % count($taskTitles)],
                        'description' => $descriptions[($companyUser->id + $i - 1) % count($descriptions)] . ' — ' . $companyUser->name . '.',
                        'priority' => $priorities[rand(0, count($priorities) - 1)],
                        'due_date' => $dueDate,
                        'estimated_duration' => $taskType->default_duration ?? rand(60, 300),
                        'case_id' => $cases->count() > 0 ? $cases->random()->id : null,
                        'assigned_to' => $users->count() > 0 ? $users->random()->id : null,
                        'task_type_id' => $taskType->id,
                        'task_status_id' => $taskStatus->id,
                        'notes' => 'Task #' . $i . ' for ' . $companyUser->name . '. Important legal work requiring attention.',
                        'tenant_id' => $companyUser->tenant_id,
                    ];
                    
                    Task::firstOrCreate([
                        'title' => $taskData['title'],
                        'tenant_id' => $companyUser->tenant_id
                    ], $taskData);
                }
            }
        }
    }
}