package com.example.phase3app;

import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class DiscussionActivity extends AppCompatActivity {

    // User info passed in from StudentHomeActivity
    private String studentId;
    private String courseId;
    private String sectionId;
    private String semester;
    private String year;

    // Whether this user is a TA/grader (enables the delete section)
    private boolean isTaOrGrader;

    // Stores the discussion_id values parallel to the display list
    private final ArrayList<String> displayList   = new ArrayList<>();
    private final ArrayList<String> discussionIds = new ArrayList<>();

    private ArrayAdapter<String> adapter;

    // Tracks which discussion_id is selected for reply or delete
    private String selectedDiscussionId = null;

    private ListView listPosts;
    private EditText editContent;
    private EditText editReplyId;
    private EditText editDeleteId;
    private TextView txtSelectedReply;
    private View     deleteSection;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_discussion);

        readIntentExtras();
        bindViews();
        setupList();
        loadPosts();
    }

    // -------------------------------------------------------------------------
    // Setup helpers
    // -------------------------------------------------------------------------

    /** Reads all extras passed by StudentHomeActivity. */
    private void readIntentExtras() {
        studentId    = getIntent().getStringExtra("user_id");
        courseId     = getIntent().getStringExtra("course_id");
        sectionId    = getIntent().getStringExtra("section_id");
        semester     = getIntent().getStringExtra("semester");
        year         = getIntent().getStringExtra("year");
        isTaOrGrader = getIntent().getBooleanExtra("is_ta_or_grader", false);

        // Fallback defaults so the screen still works from quick testing
        if (courseId  == null) courseId  = "";
        if (sectionId == null) sectionId = "";
        if (semester  == null) semester  = "Spring";
        if (year      == null) year      = "2026";
    }

    /** Binds all view references and click listeners. */
    private void bindViews() {
        listPosts       = findViewById(R.id.listPosts);
        editContent     = findViewById(R.id.editContent);
        editReplyId     = findViewById(R.id.editReplyId);
        editDeleteId    = findViewById(R.id.editDeleteId);
        txtSelectedReply = findViewById(R.id.txtSelectedReply);
        deleteSection   = findViewById(R.id.deleteSection);

        Button btnLoad   = findViewById(R.id.btnLoadPosts);
        Button btnPost   = findViewById(R.id.btnPost);
        Button btnDelete = findViewById(R.id.btnDelete);

        btnLoad.setOnClickListener(v -> loadPosts());
        btnPost.setOnClickListener(v -> submitPost());
        btnDelete.setOnClickListener(v -> deletePost());

        // Show or hide the TA/grader delete panel
        deleteSection.setVisibility(isTaOrGrader ? View.VISIBLE : View.GONE);
    }

    /** Sets up the ListView adapter and tap-to-select-reply behaviour. */
    private void setupList() {
        adapter = new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, displayList);
        listPosts.setAdapter(adapter);

        // Tapping a post pre-fills the reply-to field with its discussion_id
        listPosts.setOnItemClickListener((parent, view, position, id) -> {
            if (position < discussionIds.size()) {
                selectedDiscussionId = discussionIds.get(position);
                editReplyId.setText(selectedDiscussionId);
                txtSelectedReply.setText("Replying to post #" + selectedDiscussionId);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Network calls
    // -------------------------------------------------------------------------

    /** Fetches all posts for this section and refreshes the list. */
    private void loadPosts() {
        new Thread(() -> {
            try {
                String url = ApiClient.GET_DISCUSSION
                        + "?course_id="  + courseId
                        + "&section_id=" + sectionId
                        + "&semester="   + semester
                        + "&year="       + year;

                JSONObject json = NetworkUtils.getJson(url);

                if (!json.optBoolean("success", false)) {
                    String msg = json.optString("message", "Failed to load posts");
                    runOnUiThread(() -> Toast.makeText(this, msg, Toast.LENGTH_LONG).show());
                    return;
                }

                JSONArray posts = json.getJSONArray("posts");
                displayList.clear();
                discussionIds.clear();

                for (int i = 0; i < posts.length(); i++) {
                    JSONObject post = posts.getJSONObject(i);
                    String dId     = post.getString("discussion_id");
                    String rId     = post.getString("reply_id");
                    String author  = post.getString("student_id");
                    String content = post.getString("content");

                    // Indent replies visually
                    boolean isReply = !rId.equals("0");
                    String prefix   = isReply ? "    ↳ [Reply to #" + rId + "] " : "[#" + dId + "] ";

                    displayList.add(prefix + author + ": " + content);
                    discussionIds.add(dId);
                }

                runOnUiThread(() -> {
                    adapter.notifyDataSetChanged();
                    txtSelectedReply.setText("");
                    selectedDiscussionId = null;
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Load error: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }

    /** Submits a new post or reply. */
    private void submitPost() {
        String content   = editContent.getText().toString().trim();
        String replyToId = editReplyId.getText().toString().trim();

        if (content.isEmpty()) {
            Toast.makeText(this, "Enter a message before posting.", Toast.LENGTH_SHORT).show();
            return;
        }

        if (replyToId.isEmpty()) {
            replyToId = "0"; // top-level post
        }

        String finalReplyToId = replyToId;

        new Thread(() -> {
            try {
                Map<String, String> params = new HashMap<>();
                params.put("student_id",  studentId);
                params.put("course_id",   courseId);
                params.put("section_id",  sectionId);
                params.put("semester",    semester);
                params.put("year",        year);
                params.put("content",     content);
                params.put("reply_to_id", finalReplyToId);

                JSONObject json = NetworkUtils.postForm(ApiClient.POST_DISCUSSION, params);
                String msg = json.optString("message", "Done");

                runOnUiThread(() -> {
                    Toast.makeText(this, msg, Toast.LENGTH_LONG).show();

                    if (json.optBoolean("success", false)) {
                        editContent.setText("");
                        editReplyId.setText("");
                        txtSelectedReply.setText("");
                        selectedDiscussionId = null;
                        loadPosts();
                    }
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Post error: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }

    /** Deletes a post by discussion_id. Only available to TAs and graders. */
    private void deletePost() {
        String deleteId = editDeleteId.getText().toString().trim();

        if (deleteId.isEmpty()) {
            Toast.makeText(this, "Enter the post ID to delete.", Toast.LENGTH_SHORT).show();
            return;
        }

        new Thread(() -> {
            try {
                Map<String, String> params = new HashMap<>();
                params.put("requester_id",  studentId);
                params.put("discussion_id", deleteId);
                params.put("course_id",     courseId);
                params.put("section_id",    sectionId);
                params.put("semester",      semester);
                params.put("year",          year);

                JSONObject json = NetworkUtils.postForm(ApiClient.DELETE_DISCUSSION, params);
                String msg = json.optString("message", "Done");

                runOnUiThread(() -> {
                    Toast.makeText(this, msg, Toast.LENGTH_LONG).show();

                    if (json.optBoolean("success", false)) {
                        editDeleteId.setText("");
                        loadPosts();
                    }
                });

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Delete error: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }
}