<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\AccountHead;
use App\Models\StudentFeeManager;
use App\Services\AccountingService;

class AcademicDemoDataSeeder extends Seeder
{
    protected $classNames = ['Class One', 'Class Two', 'Class Three', 'Class Four', 'Class Five'];
    protected $sectionNames = ['A', 'B'];
    protected $studentsPerSection = 6;
    protected $teacherCount = 4;
    protected $parentCount = 10;

    /**
     * Seeds Classes, Sections, Teachers, Parents and Students (each properly
     * enrolled), then a Student Fee Manager invoice per student - most
     * marked paid, which posts a real receipt voucher through
     * AccountingService, exactly like an admin recording a payment would.
     *
     * Classes/Sections are deduplicated by name so this is safe to re-run,
     * but every run adds a fresh batch of teachers/parents/students/fees
     * on top of whatever already exists (same as AccountingDemoDataSeeder).
     */
    public function run()
    {
        (new AccountHeadSeeder())->run();

        $schoolIds = DB::table('schools')->pluck('id');

        foreach ($schoolIds as $schoolId) {
            DB::transaction(function () use ($schoolId) {
                $this->seedForSchool($schoolId);
            });
        }
    }

    protected function seedForSchool($schoolId)
    {
        $sessionId = DB::table('schools')->where('id', $schoolId)->value('running_session');

        if (empty($sessionId)) {
            return;
        }

        $faker = \Faker\Factory::create();
        $batch = now()->format('mdHis');

        $classIds = collect($this->classNames)->map(function ($name) use ($schoolId) {
            $classId = DB::table('classes')->where('school_id', $schoolId)->where('name', $name)->value('id');

            if (!$classId) {
                $classId = DB::table('classes')->insertGetId([
                    'name' => $name,
                    'school_id' => $schoolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $classId;
        });

        $sectionIdsByClass = [];
        foreach ($classIds as $classId) {
            foreach ($this->sectionNames as $sectionName) {
                $sectionId = DB::table('sections')->where('class_id', $classId)->where('name', $sectionName)->value('id');

                if (!$sectionId) {
                    $sectionId = DB::table('sections')->insertGetId([
                        'name' => $sectionName,
                        'class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $sectionIdsByClass[$classId][] = $sectionId;
            }
        }

        $parentIds = [];
        foreach (range(1, $this->parentCount) as $i) {
            $name = $faker->name();
            $parentIds[] = User::create([
                'name' => $name,
                'email' => "parent.{$schoolId}.{$batch}.{$i}@demo.ekattor.test",
                'password' => Hash::make('password'),
                'code' => 'P-'.strtoupper(uniqid()),
                'role_id' => '6',
                'school_id' => $schoolId,
                'user_information' => json_encode([
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                ]),
                'status' => 1,
            ])->id;
        }

        foreach (range(1, $this->teacherCount) as $i) {
            $name = $faker->name();
            User::create([
                'name' => $name,
                'email' => "teacher.{$schoolId}.{$batch}.{$i}@demo.ekattor.test",
                'password' => Hash::make('password'),
                'code' => 'T-'.strtoupper(uniqid()),
                'role_id' => '3',
                'school_id' => $schoolId,
                'user_information' => json_encode([
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                ]),
                'status' => 1,
            ]);
        }

        $incomeHead = AccountHead::where('school_id', $schoolId)->where('type', 'income')->where('name', 'Student Fee Income')->first();
        $accountingService = new AccountingService();
        $studentIndex = 0;

        foreach ($classIds as $classId) {
            foreach ($sectionIdsByClass[$classId] as $sectionId) {
                foreach (range(1, $this->studentsPerSection) as $s) {
                    $studentIndex++;
                    $name = $faker->name();
                    $parentId = $parentIds[array_rand($parentIds)];

                    $student = User::create([
                        'name' => $name,
                        'email' => "student.{$schoolId}.{$batch}.{$studentIndex}@demo.ekattor.test",
                        'password' => Hash::make('password'),
                        'code' => student_code(),
                        'role_id' => '7',
                        'parent_id' => $parentId,
                        'school_id' => $schoolId,
                        'user_information' => json_encode([
                            'gender' => $faker->randomElement(['Male', 'Female']),
                            'blood_group' => $faker->randomElement(['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-']),
                            'birthday' => $faker->unixTime(),
                            'phone' => $faker->phoneNumber(),
                            'address' => $faker->address(),
                            'photo' => '',
                        ]),
                        'status' => 1,
                    ]);

                    Enrollment::create([
                        'user_id' => $student->id,
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'school_id' => $schoolId,
                        'session_id' => $sessionId,
                    ]);

                    $totalAmount = $faker->randomElement([1000, 1500, 2000, 2500]);
                    $isPaid = $faker->boolean(70);
                    $paidAmount = $isPaid ? $totalAmount : 0;
                    $paymentMethod = $faker->randomElement(['cash', 'bank']);
                    $timestamp = now()->subDays(rand(0, 60))->timestamp;

                    $invoice = StudentFeeManager::create([
                        'title' => 'Monthly Tuition Fee',
                        'total_amount' => $totalAmount,
                        'amount' => $totalAmount,
                        'discounted_price' => 0,
                        'class_id' => $classId,
                        'parent_id' => $parentId,
                        'student_id' => $student->id,
                        'payment_method' => $paymentMethod,
                        'paid_amount' => $paidAmount,
                        'status' => $isPaid ? 'paid' : 'unpaid',
                        'school_id' => $schoolId,
                        'session_id' => $sessionId,
                        'timestamp' => $timestamp,
                        'account_head_id' => $incomeHead->id ?? null,
                    ]);

                    if ($isPaid && $incomeHead) {
                        $assetHead = $accountingService->resolveAssetHead($schoolId, $paymentMethod);

                        if ($assetHead) {
                            $accountingService->recordVoucher(
                                $schoolId,
                                $sessionId,
                                'receipt',
                                date('Y-m-d', $timestamp),
                                $invoice->title,
                                $assetHead->id,
                                $incomeHead->id,
                                $paidAmount,
                                'student_fee',
                                $invoice->id,
                                null
                            );
                        }
                    }
                }
            }
        }
    }
}
