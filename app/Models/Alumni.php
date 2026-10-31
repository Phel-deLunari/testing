<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';

    protected $fillable = [
        'student_code',
        'full_name',
        'email',
        'phone',
        'faculty',
        'major',
        'graduation_year',
        'job',
        'company',
        'address',
    ];

    // Accessor: Chuẩn hóa viết hoa chữ cái đầu cho Họ tên
    public function getFormattedNameAttribute(): ?string
    {
        return $this->full_name ? ucwords(strtolower($this->full_name)) : null;
    }

    // Scope: Lọc theo Khoa
    public function scopeByFaculty($query, $faculty)
    {
        return $query->where('faculty', $faculty);
    }

    // Scope: Lọc theo Năm tốt nghiệp
    public function scopeByGraduationYear($query, $year)
    {
        return $query->where('graduation_year', $year);
    }

    // Helper: Kiểm tra cựu sinh viên đã cập nhật việc làm chưa
    public function hasJob(): bool
    {
        return !empty($this->job) || !empty($this->company);
    }
}