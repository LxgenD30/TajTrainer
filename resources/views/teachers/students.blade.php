@extends('layouts.template')

@section('page-title', 'My Students')
@section('page-subtitle', 'View and manage all your students')

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">👥 My Students</h3>
        </div>

        @if(session('success'))
            <div style="background: rgba(77, 139, 49, 0.2); border: 2px solid var(--color-light-green); color: var(--color-light-green); padding: 15px; border-radius: 10px; margin: 0 20px 20px 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="card-body">
            @if($students->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(227, 216, 136, 0.1); border-bottom: 2px solid var(--color-gold);">
                                <th style="padding: 15px; text-align: left; color: var(--color-gold); font-weight: 600;">Name</th>
                                <th style="padding: 15px; text-align: left; color: var(--color-gold); font-weight: 600;">Email</th>
                                <th style="padding: 15px; text-align: left; color: var(--color-gold); font-weight: 600;">Phone</th>
                                <th style="padding: 15px; text-align: left; color: var(--color-gold); font-weight: 600;">Level</th>
                                <th style="padding: 15px; text-align: left; color: var(--color-gold); font-weight: 600;">Classes</th>
                                <th style="padding: 15px; text-align: center; color: var(--color-gold); font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr style="border-bottom: 1px solid rgba(227, 216, 136, 0.2); transition: background 0.3s ease;"
                                    onmouseover="this.style.background='rgba(227, 216, 136, 0.05)'"
                                    onmouseout="this.style.background='transparent'">
                                    <td style="padding: 15px; color: var(--color-light);">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--color-dark-green); display: flex; align-items: center; justify-content: center; color: var(--color-gold); font-weight: 600; font-size: 1.1rem;">
                                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-weight: 600;">{{ $student->user->name }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 15px; color: var(--color-light);">{{ $student->user->email }}</td>
                                    <td style="padding: 15px; color: var(--color-light);">{{ $student->phone_number ?? 'N/A' }}</td>
                                    <td style="padding: 15px;">
                                        <span style="display: inline-block; padding: 4px 12px; background: rgba(77, 139, 49, 0.2); color: var(--color-light-green); border-radius: 12px; font-size: 0.9rem; font-weight: 600;">
                                            {{ ucfirst($student->level ?? 'Beginner') }}
                                        </span>
                                    </td>
                                    <td style="padding: 15px; color: var(--color-light);">
                                        @if($student->classrooms->count() > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                                @foreach($student->classrooms as $classroom)
                                                    <span style="display: inline-block; padding: 3px 10px; background: rgba(227, 216, 136, 0.15); color: var(--color-gold); border-radius: 10px; font-size: 0.85rem;">
                                                        {{ $classroom->class_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="opacity: 0.6;">No classes</span>
                                        @endif
                                    </td>
                                    <td style="padding: 15px; text-align: center;">
                                        <a href="{{ route('students.show', $student->id) }}" 
                                            style="padding: 6px 15px; background: var(--color-dark-green); color: var(--color-gold); border-radius: 15px; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: inline-block; transition: all 0.3s ease;"
                                            onmouseover="this.style.opacity='0.8'"
                                            onmouseout="this.style.opacity='1'">
                                            View Profile
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px; padding: 15px; background: rgba(227, 216, 136, 0.05); border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: var(--color-light); font-size: 0.95rem;">
                        <strong style="color: var(--color-gold);">Total Students:</strong> {{ $students->count() }}
                    </p>
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">👥</div>
                    <h3 style="color: var(--color-light); margin-bottom: 10px;">No Students Yet</h3>
                    <p style="color: var(--color-light); opacity: 0.8; margin-bottom: 30px;">
                        You don't have any students enrolled in your classes yet.
                    </p>
                    <a href="{{ route('classroom.index') }}" 
                        style="display: inline-block; padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;"
                        onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                        View My Classes
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
