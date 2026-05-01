package com.example.phase3app;

import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RatingBar;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class EvaluationActivity extends AppCompatActivity {

    private String studentId;
    private Spinner spinnerCourses;
    private RatingBar ratingBar;
    private EditText editComment;
    private List<CourseInfo> enrolledCourses = new ArrayList<>();

    private static class CourseInfo {
        String id, section, semester, year;
        CourseInfo(String id, String section, String sem, String yr) {
            this.id = id; this.section = section; this.semester = sem; this.year = yr;
        }
        @Override public String toString() { return id + " (" + semester + " " + year + ")"; }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_evaluation);

        studentId = getIntent().getStringExtra("user_id");
        TextView txtStudentId = findViewById(R.id.txtStudentId);
        spinnerCourses = findViewById(R.id.spinnerCourses);
        ratingBar = findViewById(R.id.ratingBar);
        editComment = findViewById(R.id.editComment);
        Button btnSubmit = findViewById(R.id.btnSubmitEvaluation);

        if (studentId != null) {
            txtStudentId.setText("Student ID: " + studentId);
            loadEnrolledCourses();
        } else {
            Toast.makeText(this, "Student ID missing.", Toast.LENGTH_LONG).show();
            finish();
        }

        btnSubmit.setOnClickListener(v -> submitEvaluation());
    }

    private void loadEnrolledCourses() {
        new Thread(() -> {
            try {
                // Reusing the same logic as discussion courses to get currently enrolled ones
                JSONObject json = NetworkUtils.getJson(ApiClient.DISCUSSION_COURSES + "?student_id=" + studentId);
                if (json.optBoolean("success", false)) {
                    JSONArray arr = json.getJSONArray("courses");
                    enrolledCourses.clear();
                    List<String> labels = new ArrayList<>();
                    for (int i = 0; i < arr.length(); i++) {
                        JSONObject c = arr.getJSONObject(i);
                        // Only add courses they aren't a TA/Grader for, if you want purely "enrolled"
                        // Or just show all available.
                        CourseInfo ci = new CourseInfo(
                                c.getString("course_id"),
                                c.getString("section_id"),
                                c.getString("semester"),
                                String.valueOf(c.getInt("year"))
                        );
                        enrolledCourses.add(ci);
                        labels.add(ci.toString());
                    }
                    runOnUiThread(() -> {
                        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, labels);
                        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                        spinnerCourses.setAdapter(adapter);
                    });
                }
            } catch (Exception e) {
                runOnUiThread(() -> Toast.makeText(this, "Failed to load courses", Toast.LENGTH_SHORT).show());
            }
        }).start();
    }

    private void submitEvaluation() {
        int pos = spinnerCourses.getSelectedItemPosition();
        if (pos < 0 || enrolledCourses.isEmpty()) {
            Toast.makeText(this, "No course selected.", Toast.LENGTH_SHORT).show();
            return;
        }

        CourseInfo selected = enrolledCourses.get(pos);
        int rating = (int) ratingBar.getRating();
        String comment = editComment.getText().toString().trim();

        if (rating == 0) {
            Toast.makeText(this, "Please select a rating.", Toast.LENGTH_SHORT).show();
            return;
        }

        new Thread(() -> {
            try {
                Map<String, String> p = new HashMap<>();
                p.put("student_id", studentId);
                p.put("course_id", selected.id);
                p.put("section_id", selected.section);
                p.put("semester", selected.semester);
                p.put("year", selected.year);
                p.put("rating", String.valueOf(rating));
                p.put("comment", comment);

                JSONObject res = NetworkUtils.postForm(ApiClient.EVALUATION_SUBMIT, p);
                String msg = res.optString("message", "Result");
                runOnUiThread(() -> {
                    Toast.makeText(this, msg, Toast.LENGTH_LONG).show();
                    if (res.optBoolean("success")) finish();
                });
            } catch (Exception e) {
                runOnUiThread(() -> Toast.makeText(this, "Error: " + e.getMessage(), Toast.LENGTH_SHORT).show());
            }
        }).start();
    }
}