package com.example.phase3app;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONObject;

public class RetrieveLoginInfoActivity extends AppCompatActivity {

    private EditText editEmail;
    private TextView txtLoginInfo;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retrieve_login_info);

        editEmail = findViewById(R.id.editEmail);
        txtLoginInfo = findViewById(R.id.txtLoginInfo);
        Button btnRetrieveLoginInfo = findViewById(R.id.btnRetrieveLoginInfo);

        btnRetrieveLoginInfo.setOnClickListener(v -> retrieveLoginInfo());
    }

    private void retrieveLoginInfo() {
        String email = editEmail.getText().toString().trim();

        if (email.isEmpty()) {
            Toast.makeText(this, "Enter email", Toast.LENGTH_LONG).show();
            return;
        }

        new Thread(() -> {
            try {
                JSONObject json = NetworkUtils.getJson(
                        ApiClient.RETRIEVE_LOGIN_INFO + "?email=" + email
                );

                boolean success = json.getBoolean("success");
                if (!success) {
                    String message = json.getString("message");
                    runOnUiThread(() -> txtLoginInfo.setText(message));
                    return;
                }

                String type = json.getString("type");
                String userId = json.getString("user_id");

                runOnUiThread(() ->
                        txtLoginInfo.setText("Type: " + type + "\nID: " + userId + "\nEmail: " + email)
                );

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Retrieve error: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }
}