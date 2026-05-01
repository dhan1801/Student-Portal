package com.example.phase3app;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class StudentHomeActivity extends AppCompatActivity {

    private String userId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_student_home);

        userId = getIntent().getStringExtra("user_id");

        TextView txtWelcome = findViewById(R.id.txtWelcome);

        // Guard: check valid user before binding any buttons
        if (userId == null || userId.trim().isEmpty()) {
            txtWelcome.setText(getString(R.string.invalid_user_message));
            Toast.makeText(this, getString(R.string.student_id_missing), Toast.LENGTH_LONG).show();
            return;
        }

        txtWelcome.setText(getString(R.string.logged_in_as, userId));

        Button btnBrowseRegister = findViewById(R.id.btnBrowseRegister);
        Button btnMyEnrollments  = findViewById(R.id.btnMyEnrollments);
        Button btnTranscript     = findViewById(R.id.btnTranscript);
        Button btnDiscussion     = findViewById(R.id.btnDiscussion);
        Button btnEvaluation     = findViewById(R.id.btnEvaluation);
        Button btnLogout         = findViewById(R.id.btnLogout);

        if (btnBrowseRegister != null)
            btnBrowseRegister.setOnClickListener(v -> openActivity(BrowseRegisterActivity.class));

        if (btnMyEnrollments != null)
            btnMyEnrollments.setOnClickListener(v -> openActivity(MyEnrollmentsActivity.class));

        if (btnTranscript != null)
            btnTranscript.setOnClickListener(v -> openActivity(TranscriptActivity.class));

        // Discussion → course picker first, then discussion board
        if (btnDiscussion != null)
            btnDiscussion.setOnClickListener(v -> openActivity(CoursePickerActivity.class));

        if (btnEvaluation != null)
            btnEvaluation.setOnClickListener(v -> openActivity(EvaluationActivity.class));

        // Logout: clear the back stack and return to login screen
        if (btnLogout != null)
            btnLogout.setOnClickListener(v -> logout());
    }

    private void openActivity(Class<?> target) {
        Intent intent = new Intent(this, target);
        intent.putExtra("user_id", userId);
        startActivity(intent);
    }

    private void logout() {
        Intent intent = new Intent(this, MainActivity.class);
        // Clear the entire back stack so pressing Back does not return here
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(intent);
        finish();
    }
}