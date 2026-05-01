package com.example.phase3app;

import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class BrowseRegisterActivity extends AppCompatActivity {

    private String studentId;
    private ListView listCourses;
    private EditText editCourseId;
    private EditText editSectionId;
    private ArrayAdapter<String> adapter;
    private final ArrayList<String> courseList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_browse_register);

        studentId = getIntent().getStringExtra("user_id");

        listCourses = findViewById(R.id.listCourses);
        editCourseId = findViewById(R.id.editCourseId);
        editSectionId = findViewById(R.id.editSectionId);
        Button btnLoadCourses = findViewById(R.id.btnLoadCourses);
        Button btnRegister = findViewById(R.id.btnRegister);

        adapter = new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, courseList);
        listCourses.setAdapter(adapter);

        btnLoadCourses.setOnClickListener(v -> loadCourses());
        btnRegister.setOnClickListener(v -> registerCourse());
    }

    private void loadCourses() {
        new Thread(() -> {
            try {
                JSONObject json = NetworkUtils.getJson(
                        ApiClient.BROWSE_COURSES + "?semester=Spring&year=2026"
                );

                if (!json.optBoolean("success", false)) {
                    String message = json.optString("message", "Failed to load courses");
                    runOnUiThread(() ->
                            Toast.makeText(this, message, Toast.LENGTH_LONG).show()
                    );
                    return;
                }

                JSONArray courses = json.getJSONArray("courses");
                courseList.clear();

                for (int i = 0; i < courses.length(); i++) {
                    JSONObject c = courses.getJSONObject(i);

                    String item = c.getString("course_id") + " | "
                            + c.getString("title") + " | Sec "
                            + c.getString("section_id") + " | "
                            + c.getString("building") + " "
                            + c.getString("room_number") + " | "
                            + c.getString("day") + " "
                            + String.format("%02d:%02d-%02d:%02d",
                            c.getInt("start_hour"),
                            c.getInt("start_min"),
                            c.getInt("end_hour"),
                            c.getInt("end_min"))
                            + " | Enrolled: "
                            + c.getInt("enrolled") + "/"
                            + c.getInt("max_allowed");

                    courseList.add(item);
                }

                runOnUiThread(() -> adapter.notifyDataSetChanged());

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Load failed: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }

    private void registerCourse() {
        String courseId = editCourseId.getText().toString().trim();
        String sectionId = editSectionId.getText().toString().trim();

        if (studentId == null || studentId.isEmpty()) {
            Toast.makeText(this, "Student ID missing. Please log in again.", Toast.LENGTH_LONG).show();
            return;
        }

        if (courseId.isEmpty() || sectionId.isEmpty()) {
            Toast.makeText(this, "Enter Course ID and Section ID", Toast.LENGTH_SHORT).show();
            return;
        }

        new Thread(() -> {
            try {
                Map<String, String> params = new HashMap<>();
                params.put("student_id", studentId);
                params.put("course_id", courseId);
                params.put("section_id", sectionId);
                params.put("semester", "Spring");
                params.put("year", "2026");

                JSONObject json = NetworkUtils.postForm(ApiClient.REGISTER_COURSE, params);
                String message = json.getString("message");

                runOnUiThread(() -> {
                    Toast.makeText(this, message, Toast.LENGTH_LONG).show();
                    loadCourses();
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Register failed: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }
}