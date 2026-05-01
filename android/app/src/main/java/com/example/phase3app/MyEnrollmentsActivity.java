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

public class MyEnrollmentsActivity extends AppCompatActivity {

    private String studentId;
    private ArrayAdapter<String> adapter;
    private final ArrayList<String> data = new ArrayList<>();
    private EditText editDropCourseId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_enrollments);

        studentId = getIntent().getStringExtra("user_id");

        ListView list          = findViewById(R.id.listEnrollments);
        editDropCourseId       = findViewById(R.id.editDropCourseId);
        Button btnLoad         = findViewById(R.id.btnLoadEnrollments);
        Button btnDrop         = findViewById(R.id.btnDrop);

        adapter = new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, data);
        list.setAdapter(adapter);

        btnLoad.setOnClickListener(v -> loadEnrollments());
        btnDrop.setOnClickListener(v -> dropCourse());
    }

    /** Loads current enrollments (grade IS NULL) for Spring 2026. */
    private void loadEnrollments() {
        new Thread(() -> {
            try {
                String url = ApiClient.CURRENT_ENROLLMENTS
                        + "?student_id=" + studentId
                        + "&semester=Spring&year=2026";

                JSONObject json = NetworkUtils.getJson(url);

                if (!json.getBoolean("success")) {
                    String msg = json.optString("message", "Load failed");
                    runOnUiThread(() -> Toast.makeText(this, msg, Toast.LENGTH_LONG).show());
                    return;
                }

                JSONArray arr = json.getJSONArray("courses");
                data.clear();

                for (int i = 0; i < arr.length(); i++) {
                    JSONObject c = arr.getJSONObject(i);
                    data.add(c.getString("course_id") + " | "
                            + c.getString("title") + " | Sec "
                            + c.getString("section_id"));
                }

                runOnUiThread(() -> adapter.notifyDataSetChanged());

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Load failed: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }

    /** Drops a course by course_id. */
    private void dropCourse() {
        String courseId = editDropCourseId.getText().toString().trim();

        if (courseId.isEmpty()) {
            Toast.makeText(this, "Enter Course ID to drop", Toast.LENGTH_LONG).show();
            return;
        }

        new Thread(() -> {
            try {
                Map<String, String> p = new HashMap<>();
                p.put("student_id",  studentId);
                p.put("course_id",   courseId);
                p.put("section_id",  "1");
                p.put("semester",    "Spring");
                p.put("year",        "2026");

                JSONObject j   = NetworkUtils.postForm(ApiClient.DROP_COURSE, p);
                String    msg  = j.optString("message", "Done");

                runOnUiThread(() -> {
                    Toast.makeText(this, msg, Toast.LENGTH_LONG).show();
                    loadEnrollments();
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Drop failed: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }
}