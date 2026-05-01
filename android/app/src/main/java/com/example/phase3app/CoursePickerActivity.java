package com.example.phase3app;

import android.content.Intent;
import android.os.Bundle;
import android.util.TypedValue;
import android.view.Gravity;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * Shows all courses a student can access on the discussion board:
 *   - Courses they are currently enrolled in (grade IS NULL)
 *   - Courses they are TA or grader for this semester
 *
 * The is_ta_or_grader flag comes back from the server in each course row,
 * so no separate per-button network call is required.
 */
public class CoursePickerActivity extends AppCompatActivity {

    private String studentId;
    private LinearLayout courseButtonContainer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_course_picker);

        studentId             = getIntent().getStringExtra("user_id");
        courseButtonContainer = findViewById(R.id.courseButtonContainer);

        if (studentId == null || studentId.trim().isEmpty()) {
            Toast.makeText(this, "Student ID missing.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        loadDiscussionCourses();
    }

    /**
     * Single API call: returns enrolled courses PLUS TA/grader courses,
     * each with a pre-computed is_ta_or_grader boolean.
     */
    private void loadDiscussionCourses() {
        new Thread(() -> {
            try {
                JSONObject json = NetworkUtils.getJson(
                        ApiClient.DISCUSSION_COURSES + "?student_id=" + studentId
                );

                if (!json.optBoolean("success", false)) {
                    runOnUiThread(() -> Toast.makeText(this,
                            json.optString("message", "Failed to load courses"),
                            Toast.LENGTH_LONG).show());
                    return;
                }

                JSONArray courses = json.getJSONArray("courses");

                if (courses.length() == 0) {
                    runOnUiThread(() -> Toast.makeText(this,
                            "No courses available for discussion.",
                            Toast.LENGTH_LONG).show());
                    return;
                }

                runOnUiThread(() -> {
                    courseButtonContainer.removeAllViews();

                    for (int i = 0; i < courses.length(); i++) {
                        try {
                            JSONObject course     = courses.getJSONObject(i);
                            String courseId       = course.getString("course_id");
                            String sectionId      = course.getString("section_id");
                            String title          = course.getString("title");
                            String semester       = course.getString("semester");
                            String year           = String.valueOf(course.getInt("year"));
                            boolean isTaOrGrader  = course.optBoolean("is_ta_or_grader", false);

                            // Show "(TA)" or "(Grader)" suffix so the role is clear
                            String roleTag = isTaOrGrader ? "  [TA/Grader]" : "";
                            String label = courseId + " - " + title
                                    + "\nSection " + sectionId
                                    + "  |  " + semester + " " + year
                                    + roleTag;

                            Button btn = new Button(this);
                            btn.setText(label);
                            btn.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14f);
                            btn.setAllCaps(false);
                            btn.setGravity(Gravity.CENTER);

                            int marginPx = (int) TypedValue.applyDimension(
                                    TypedValue.COMPLEX_UNIT_DIP, 12,
                                    getResources().getDisplayMetrics()
                            );
                            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                                    LinearLayout.LayoutParams.MATCH_PARENT,
                                    LinearLayout.LayoutParams.WRAP_CONTENT
                            );
                            params.setMargins(0, 0, 0, marginPx);
                            btn.setLayoutParams(params);

                            // Pass the flag directly — no second network call needed
                            btn.setOnClickListener(v ->
                                    openDiscussion(courseId, sectionId, semester, year, isTaOrGrader)
                            );
                            courseButtonContainer.addView(btn);

                        } catch (Exception ignored) {}
                    }
                });

            } catch (Exception e) {
                runOnUiThread(() -> Toast.makeText(this,
                        "Load error: " + e.getMessage(), Toast.LENGTH_LONG).show());
            }
        }).start();
    }

    private void openDiscussion(String courseId, String sectionId,
                                String semester, String year, boolean isTaOrGrader) {
        Intent intent = new Intent(this, DiscussionActivity.class);
        intent.putExtra("user_id",         studentId);
        intent.putExtra("course_id",       courseId);
        intent.putExtra("section_id",      sectionId);
        intent.putExtra("semester",        semester);
        intent.putExtra("year",            year);
        intent.putExtra("is_ta_or_grader", isTaOrGrader);
        startActivity(intent);
    }
}