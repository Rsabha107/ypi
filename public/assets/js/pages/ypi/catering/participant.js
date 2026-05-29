$(document).ready(function () {
    console.log('Catering participant.js loaded');

    // QID Image Modal
    $(document).on('click', '.qid-image-link', function (e) {
        e.preventDefault();
        const imageUrl = $(this).data('image-url');
        $('#qidImagePreview').attr('src', imageUrl);
        const qidModal = new bootstrap.Modal(document.getElementById('qidImageModal'));
        qidModal.show();
    });

    // Participant Name Link - Show Details Modal
    $(document).on('click', '.participant-name-link', function (e) {
        e.preventDefault();
        const participantId = $(this).data('participant-id');
        
        // Fetch participant details
        $.ajax({
            url: `/ypi/catering/participant/${participantId}/details`,
            method: 'GET',
            success: function (response) {
                const participant = response.participant;
                
                // Personal Information
                $('#modal_full_name').text(participant.full_name || '-');
                $('#modal_qid').text(participant.qid || '-');
                $('#modal_date_of_birth').text(participant.date_of_birth || '-');
                $('#modal_gender').text(participant.gender || '-');
                $('#modal_nationality').text(participant.nationality || '-');
                $('#modal_school_name').text(participant.school_name || '-');
                
                // Event Information
                $('#modal_event').text(participant.event || '-');
                $('#modal_participant_type').text(participant.participant_type || '-');
                
                // Status with badge
                if (participant.status) {
                    const statusBadge = `<span class="badge badge-phoenix fs--2 badge-phoenix-${participant.status_color}">
                        ${participant.status}
                    </span>`;
                    $('#modal_status').html(statusBadge);
                } else {
                    $('#modal_status').text('-');
                }
                
                $('#modal_assigned_venue').text(participant.assigned_venue || '-');
                $('#modal_assigned_match').text(participant.assigned_match || '-');
                
                // Sizes
                $('#modal_pants_size').text(participant.pants_size || '-');
                $('#modal_jersey_size').text(participant.jersey_size || '-');
                $('#modal_jacket_size').text(participant.jacket_size || '-');
                $('#modal_shoe_size').text(participant.shoe_size || '-');
                
                // Medical Information
                $('#modal_food_allergy').text(participant.food_allergy || '-');
                $('#modal_food_allergy_type').text(participant.food_allergy_type || '-');
                
                if (participant.food_allergy_others) {
                    $('#modal_food_allergy_others').text(participant.food_allergy_others);
                    $('#food_allergy_others_section').show();
                } else {
                    $('#food_allergy_others_section').hide();
                }
                
                $('#modal_health_issues').text(participant.health_issues || '-');
                
                if (participant.health_issues_details) {
                    $('#modal_health_issues_details').text(participant.health_issues_details);
                    $('#health_issues_details_section').show();
                } else {
                    $('#health_issues_details_section').hide();
                }
                
                // Guardian Information
                $('#modal_guardian_name').text(participant.guardian_name || '-');
                $('#modal_guardian_email').text(participant.guardian_email || '-');
                $('#modal_guardian_phone').text(participant.guardian_phone || '-');
                
                // Show modal
                const detailsModal = new bootstrap.Modal(document.getElementById('participantDetailsModal'));
                detailsModal.show();
            },
            error: function (xhr) {
                console.error('Error fetching participant details:', xhr);
                toastr.error('Failed to load participant details');
            }
        });
    });
});
