<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users in table format
     */
    public function index()
    {
        $users = User::all();
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'table' => $this->formatAsTable($users)
        ]);
    }

    /**
     * Store a newly created user (similar to register but for admin use)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => [
                'required_if:role,student',
                'string',
                'nullable',
                'regex:/^\d{4}-\d-\d{5}$/',
                Rule::unique('users')->ignore($request->id)
            ],
            'role' => 'required|in:student,nurse,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'student_id' => $request->student_id,
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
            'table' => $this->formatAsTable([$user]),
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user,
            'table' => $this->formatAsTable([$user])
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
            'student_id' => [
                'sometimes',
                'string',
                'nullable',
                'regex:/^\d{4}-\d-\d{5}$/',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'sometimes|in:student,nurse,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'user' => $user,
            'table' => $this->formatAsTable([$user]),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Helper method to format users as HTML table
     */
    private function formatAsTable($users)
    {
        $html = '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Student ID</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($users as $user) {
            $html .= '<tr>
                <td>'.$user->id.'</td>
                <td>'.$user->name.'</td>
                <td>'.$user->email.'</td>
                <td>'.$user->student_id.'</td>
                <td>'.$user->role.'</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}