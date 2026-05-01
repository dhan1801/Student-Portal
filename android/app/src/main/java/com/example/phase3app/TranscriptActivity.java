package com.example.phase3app;

import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

public class TranscriptActivity extends AppCompatActivity {

    private String studentId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_transcript);

        studentId = getIntent().getStringExtra("user_id");
        TextView txtTranscript = findViewById(R.id.txtTranscript);

        if (studentId == null || studentId.isEmpty()) {
            txtTranscript.setText("Error: student ID missing.");
            return;
        }

        loadTranscript(txtTranscript);
    }

    private void loadTranscript(TextView txtTranscript) {
        new Thread(() -> {
            try {
                JSONObject json = NetworkUtils.getJson(
                        ApiClient.TRANSCRIPT + "?student_id=" + studentId
                );

                if (!json.optBoolean("success", false)) {
                    String msg = json.optString("message", "Failed to load transcript");
                    runOnUiThread(() -> txtTranscript.setText(msg));
                    return;
                }

                JSONArray courses      = json.getJSONArray("courses");
                double   gpa           = json.optDouble("gpa", 0.0);
                int      earnedCredits = json.optInt("earned_credits", 0);

                StringBuilder sb = new StringBuilder();
                sb.append("Cumulative GPA: ").append(gpa).append("\n");
                sb.append("Credits Earned: ").append(earnedCredits).append("\n\n");
                sb.append("─────────────────────────\n");

                for (int i = 0; i < courses.length(); i++) {
                    JSONObject c     = courses.getJSONObject(i);
                    String courseId  = c.getString("course_id");
                    String title     = c.optString("title", "");
                    String semester  = c.optString("semester", "");
                    int    yr        = c.optInt("year", 0);
                    String grade     = c.optString("grade", "In Progress");
                    String sectionId = c.optString("section_id", "1");

                    sb.append(courseId).append(" - ").append(title).append("\n");
                    sb.append(semester).append(" ").append(yr);
                    sb.append("  |  Grade: ").append(grade).append("\n");

                    // If grade is "Evaluation Required", show a tap hint
                    if ("Evaluation Required".equals(grade)) {
                        sb.append("  ⚠ Tap here to submit evaluation\n");
                    }
                    sb.append("\n");
                }

                runOnUiThread(() -> {
                    txtTranscript.setText(sb.toString());

                    // Make the TextView tappable to open evaluation for courses needing it
                    txtTranscript.setOnClickListener(v -> {
                        try {
                            for (int i = 0; i < courses.length(); i++) {
                                JSONObject c = courses.getJSONObject(i);
                                if ("Evaluation Required".equals(c.optString("grade", ""))) {
                                    openEvaluation(
                                            c.getString("course_id"),
                                            c.optString("section_id", "1"),
                                            c.optString("semester", "Spring"),
                                            String.valueOf(c.optInt("year", 2026))
                                    );
                                    return;
                                }
                            }
                            Toast.makeText(this,
                                    "No pending evaluations.", Toast.LENGTH_SHORT).show();
                        } catch (Exception e) {
                            Toast.makeText(this,
                                    "Error: " + e.getMessage(), Toast.LENGTH_SHORT).show();
                        }
                    });
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        txtTranscript.setText("Error loading transcript: " + e.getMessage())
                );
            }
        }).start();
    }

    /** Opens EvaluationActivity for the given course. */
    private void openEvaluation(String courseId, String sectionId,
                                String semester, String year) {
        Intent intent = new Intent(this, EvaluationActivity.class);
        intent.putExtra("user_id",    studentId);
        intent.putExtra("course_id",  courseId);
        intent.putExtra("section_id", sectionId);
        intent.putExtra("semester",   semester);
        intent.putExtra("year",       year);
        startActivity(intent);
    }
}