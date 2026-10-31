<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Alumni;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlumniTest extends TestCase
{
    // Tự động Rollback sau mỗi test case, giữ nguyên dữ liệu thật của nhóm
    use DatabaseTransactions;

    /**
     * TC01: Kiểm tra việc đọc dữ liệu có sẵn từ database của nhóm
     */
    public function test_can_read_existing_alumni_data()
    {
        // Kiểm tra bản ghi mẫu có sẵn trong database quanlycuusinhvien
        $this->assertDatabaseHas('alumni', [
            'student_code' => '23010173',
            'full_name'    => 'Nguyen Quoc Quang Anh',
            'faculty'      => 'CNTT',
        ]);
    }

    /**
     * TC02: Thêm mới cựu sinh viên với đầy đủ thông tin hợp lệ
     */
    public function test_can_create_alumni_with_full_information()
    {
        $alumni = Alumni::create([
            'student_code'    => '23010171',
            'full_name'       => 'Nguyen Minh Duc',
            'email'           => '23010171@phenikaa-uni.edu.vn',
            'phone'           => '0987654321',
            'faculty'         => 'CNTT',
            'major'           => 'K17-CNTT_2',
            'graduation_year' => 2027,
            'job'             => 'Software QA Engineer',
            'company'         => 'Phenikaa Group',
            'address'         => 'Ha Noi',
        ]);

        $this->assertInstanceOf(Alumni::class, $alumni);
        $this->assertEquals('Nguyen Minh Duc', $alumni->full_name);
        $this->assertDatabaseHas('alumni', [
            'student_code' => '23010171',
            'email'        => '23010171@phenikaa-uni.edu.vn',
        ]);
    }

    /**
     * TC03: Kiểm tra tính toàn vẹn dữ liệu - Bắt buộc phải có full_name (NOT NULL)
     */
    public function test_cannot_create_alumni_without_full_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Alumni::create([
            'student_code'    => '23019999',
            'email'           => 'no_name@gmail.com',
            'faculty'         => 'CNTT',
            'graduation_year' => 2027,
        ]);
    }

    /**
     * TC04: Kiểm tra logic chuẩn hóa định dạng Họ và tên (Accessor)
     */
    public function test_formatted_name_accessor()
    {
        $alumni = new Alumni(['full_name' => 'nguyen van a']);
        $this->assertEquals('Nguyen Van A', $alumni->formatted_name);
    }

    /**
     * TC05: Cập nhật thông tin công việc và cơ quan của cựu sinh viên
     */
    public function test_update_alumni_career_information()
    {
        $alumni = Alumni::where('student_code', '23010800')->first();

        $alumni->update([
            'job'     => 'Senior Backend Developer',
            'company' => 'FPT Software',
        ]);

        $this->assertEquals('Senior Backend Developer', $alumni->fresh()->job);
        $this->assertEquals('FPT Software', $alumni->fresh()->company);
        $this->assertTrue($alumni->hasJob());
    }

    /**
     * TC06: Kiểm tra Scope lọc cựu sinh viên theo Khoa (faculty)
     */
    public function test_scope_filter_by_faculty()
    {
        $cnttAlumni = Alumni::byFaculty('CNTT')->get();

        $this->assertGreaterThan(0, $cnttAlumni->count());
        foreach ($cnttAlumni as $alumni) {
            $this->assertEquals('CNTT', $alumni->faculty);
        }
    }

    /**
     * TC07: Kiểm tra Scope lọc cựu sinh viên theo Năm tốt nghiệp (graduation_year)
     */
    public function test_scope_filter_by_graduation_year()
    {
        $grad2027 = Alumni::byGraduationYear(2027)->get();

        $this->assertGreaterThan(0, $grad2027->count());
        foreach ($grad2027 as $alumni) {
            $this->assertEquals(2027, $alumni->graduation_year);
        }
    }
}