<?php
session_start();

$sidebarMode = isset($_SESSION['sidebar_mode']) ? $_SESSION['sidebar_mode'] : 'manual';

include 'Sidebar.php';
include 'NavigationBar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceCo - App Banner & Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
            user-select: none;
        }

        .app-content {
            margin-top: 70px;
            padding: 20px;
            margin-left: 240px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 240px);
            min-height: calc(100vh - 70px);
        }

        .sidebar.collapsed ~ .navbar,
        .sidebar.auto-hide ~ .navbar {
            left: 70px !important;
        }

        .sidebar.auto-hide:hover ~ .navbar {
            left: 240px !important;
        }

        .sidebar.collapsed ~ .app-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .sidebar.auto-hide ~ .app-content {
            margin-left: 70px !important;
            width: calc(100% - 70px) !important;
        }

        .sidebar.auto-hide:hover ~ .app-content {
            margin-left: 240px !important;
            width: calc(100% - 240px) !important;
        }

        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .welcome-subtitle {
            color: #666;
            font-size: 14px;
        }

        .main-layout {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        .banner-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: none;
            height: auto;
        }

        .section-header {
            padding: 15px 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .section-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .section-badge {
            background-color: #3b82f6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.active {
            background-color: #d1fae5;
            color: #10b981;
        }

        .status-badge.inactive {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .status-badge.draft {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .banner-grid-container {
            padding: 20px;
            flex: 1;
            max-height: 450px;
            overflow-y: auto;
            background-color: white;
        }

        .banner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .banner-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            transition: all 0.2s;
            background-color: white;
            cursor: grab;
            height: 180px;
            display: flex;
            flex-direction: column;
            position: relative;
            user-select: none;
        }

        .banner-card:active {
            cursor: grabbing;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            opacity: 0.9;
            transform: scale(0.98);
        }

        .banner-card.dragging {
            opacity: 0.5;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
            border: 2px dashed #3b82f6;
            cursor: grabbing;
        }

        .banner-card.drag-over {
            border: 2px dashed #3b82f6;
            background-color: #f0f9ff;
            transform: scale(1.02);
        }

        .banner-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #3b82f6;
        }

        .banner-image {
            flex: 1;
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }

        .banner-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }

        .banner-card:hover .banner-overlay {
            opacity: 1;
        }

        .banner-actions {
            display: flex;
            gap: 8px;
            pointer-events: auto;
        }

        .banner-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: white;
            border: none;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .banner-action-btn:hover {
            background-color: #3b82f6;
            color: white;
            transform: scale(1.1);
        }

        .banner-info {
            padding: 12px;
            flex-shrink: 0;
            border-top: 1px solid #f3f4f6;
            pointer-events: none;
        }

        .banner-title {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .banner-meta {
            font-size: 11px;
            color: #6b7280;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-management {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 0;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .order-header {
            padding: 15px 20px;
            background-color: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
        }

        .order-header:hover {
            background-color: #f3f4f6;
        }

        .order-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-title i {
            color: #3b82f6;
        }

        .order-toggle {
            color: #6b7280;
            transition: transform 0.3s ease;
        }

        .order-toggle.collapsed {
            transform: rotate(-90deg);
        }

        .order-content {
            padding: 20px;
            display: block;
            transition: all 0.3s ease;
            max-height: 300px;
            overflow-y: auto;
        }

        .order-content.collapsed {
            display: none;
        }

        .order-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .order-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .order-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        .order-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .order-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .order-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0;
        }

        .order-banner-title {
            font-size: 13px;
            color: #333;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-banner-status {
            font-size: 11px;
            color: #10b981;
        }

        .order-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .order-btn {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background-color: white;
            border: 1px solid #e5e7eb;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .order-btn:hover:not(:disabled) {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .order-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .order-dropdown {
            width: 70px;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            background-color: white;
            cursor: pointer;
        }

        .order-dropdown:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .upload-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .upload-container {
            padding: 20px;
        }

        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #333;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .required {
            color: #ef4444;
        }

        .form-input {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            width: 100%;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            background-color: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background-color: #f0f9ff;
        }

        .upload-area.dragover {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .upload-icon {
            font-size: 32px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .upload-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .upload-subtext {
            color: #9ca3af;
            font-size: 12px;
        }

        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .upload-preview {
            margin-top: 15px;
            display: none;
        }

        .preview-container {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .preview-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background-color: #f3f4f6;
        }

        .preview-info {
            padding: 12px;
            background-color: white;
        }

        .preview-name {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .preview-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #6b7280;
        }

        .date-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-icon {
            color: #6b7280;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex: 1;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .draft-section {
            margin-top: 20px;
        }

        .draft-banners {
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .draft-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
        }

        .draft-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }

        .draft-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        .draft-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .draft-image {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .draft-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .draft-text {
            flex: 1;
            min-width: 0;
        }

        .draft-title {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .draft-date {
            font-size: 11px;
            color: #6b7280;
        }

        .draft-actions {
            display: flex;
            gap: 5px;
            flex-shrink: 0;
        }

        .draft-btn {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            background-color: white;
            border: 1px solid #e5e7eb;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .draft-btn:hover {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .no-drafts {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 14px;
        }

        .status-toggle {
            display: flex;
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 4px;
            width: fit-content;
        }

        .toggle-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: none;
            color: #6b7280;
            transition: all 0.2s;
        }

        .toggle-btn.active {
            background-color: white;
            color: #3b82f6;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .notification-message {
            position: fixed;
            top: 90px;
            right: 25px;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 13px;
            max-width: 300px;
            display: none;
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
        }

        .notification-message.success {
            background-color: #10b981;
            color: white;
            border-left: 4px solid #059669;
        }

        .notification-message.error {
            background-color: #ef4444;
            color: white;
            border-left: 4px solid #dc2626;
        }

        .notification-message.warning {
            background-color: #f59e0b;
            color: white;
            border-left: 4px solid #d97706;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1100;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            overflow: hidden;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 18px 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: #e5e7eb;
            color: #374151;
        }

        .modal-body {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 15px 20px;
            background-color: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .preview-modal .modal-content {
            max-width: 600px;
        }

        .modal-image-container {
            width: 100%;
            max-height: 400px;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .modal-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .file-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .file-info-item {
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .file-info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .file-info-value {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            word-break: break-all;
        }

        .delete-modal .confirmation-icon {
            font-size: 48px;
            color: #ef4444;
            margin-bottom: 20px;
            text-align: center;
        }

        .confirmation-message {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .warning-text {
            color: #ef4444;
            font-size: 14px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1200;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            text-align: center;
            padding: 15px;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-text {
            font-size: 14px;
        }

        .upload-status {
            margin-top: 10px;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            display: none;
        }

        .upload-status.uploading {
            background-color: #dbeafe;
            color: #1e40af;
            display: block;
        }

        .upload-status.success {
            background-color: #d1fae5;
            color: #065f46;
            display: block;
        }

        .upload-status.error {
            background-color: #fee2e2;
            color: #991b1b;
            display: block;
        }

        @media (max-width: 768px) {
            .app-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 15px;
            }
            
            .main-layout {
                gap: 15px;
            }
            
            .banner-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .banner-grid-container {
                max-height: 400px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .modal-image-container {
                height: 250px;
            }
            
            .file-info-grid {
                grid-template-columns: 1fr;
            }
            
            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .order-actions {
                align-self: flex-end;
            }
            
            .upload-section {
                position: static;
                top: auto;
            }
        }

        @media (max-width: 576px) {
            .app-content {
                padding: 12px;
                margin-left: 0;
                width: 100%;
            }
            
            .section-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .section-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .banner-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .banner-card {
                height: 160px;
            }
            
            .modal {
                padding: 10px;
            }
            
            .modal-content {
                width: 100%;
                max-height: 95vh;
            }
        }

        @media (min-width: 577px) and (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
            
            .upload-section {
                order: -1;
                position: static;
            }
            
            .banner-grid-container {
                max-height: 450px;
            }
        }
    </style>
</head>
<body data-sidebar-mode="<?php echo $sidebarMode; ?>">
    
    <div class="app-content">
        <div class="welcome-section">
            <h1 class="welcome-title">App Banner & Announcements</h1>
            <p class="welcome-subtitle">Manage the sliding banners and public advisories displayed on the commuter app dashboard.</p>
        </div>

        <div class="main-layout">
            <div class="banner-section">
                <div class="section-header">
                    <div class="section-title">Banner Management</div>
                    <div class="section-actions">
                        <div class="status-toggle">
                            <button class="toggle-btn active" onclick="filterBanners('active')">Active</button>
                            <button class="toggle-btn" onclick="filterBanners('inactive')">Inactive</button>
                            <button class="toggle-btn" onclick="filterBanners('all')">All</button>
                        </div>
                        <div class="section-badge" id="bannerCount">0 Banners</div>
                    </div>
                </div>

                <div class="banner-grid-container" id="bannerGridContainer">
                    <div class="banner-grid" id="bannersGrid">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <div class="empty-text">No banners found</div>
                            <div class="empty-text" style="font-size: 12px; margin-top: 5px;">Upload your first banner on the right</div>
                        </div>
                    </div>
                </div>

                <div class="order-management" id="orderManagement" style="display: none;">
                    <div class="order-header" onclick="toggleOrderSection()">
                        <div class="order-title">
                            <i class="fas fa-sort-amount-down"></i>
                            Display Order Management
                        </div>
                        <div class="order-toggle" id="orderToggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="order-content" id="orderContent">
                        <div class="order-list" id="orderList">
                        </div>
                    </div>
                </div>
            </div>

            <div class="upload-section">
                <div class="section-header">
                    <div class="section-title">Upload New Banner</div>
                </div>
                
                <div class="upload-container">
                    <form class="upload-form" id="bannerUploadForm">
                        <input type="hidden" id="currentBannerId" value="">
                        <input type="hidden" id="currentBannerStatus" value="new">
                        
                        <div class="form-group">
                            <label class="form-label">
                                Campaign Title 
                                <span class="required">*</span>
                            </label>
                            <input type="text" class="form-input" id="bannerTitle" placeholder="Enter campaign title" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Upload Banner Image 
                                <span class="required">*</span>
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">Drop Files Here or Click to Browse</div>
                                <div class="upload-subtext">Recommended: 1080×540px (Max: 5MB)</div>
                                <input type="file" class="file-input" id="bannerFile" accept="image/*" onchange="handleFileSelect(event)">
                            </div>
                            
                            <div class="upload-preview" id="uploadPreview">
                                <div class="preview-container">
                                    <img class="preview-image" id="previewImage" alt="Preview">
                                    <div class="preview-info">
                                        <div class="preview-name" id="previewFileName"></div>
                                        <div class="preview-details">
                                            <span id="previewFileSize"></span>
                                            <span id="previewDimensions"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="upload-status" id="uploadStatus"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Optional Redirect Link</label>
                            <input type="url" class="form-input" id="redirectLink" placeholder="//app/promos/your-campaign">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Expiry Date 
                                <span class="required">*</span>
                            </label>
                            <div class="date-input">
                                <input type="date" class="form-input" id="expiryDate" required>
                                <i class="fas fa-calendar-alt calendar-icon"></i>
                            </div>
                        </div>

                        <div class="button-group" id="uploadButtonGroup">
                            <button type="button" class="btn btn-secondary" onclick="saveAsDraft()">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="submit" class="btn btn-primary" id="uploadPublishBtn">
                                <i class="fas fa-upload"></i> Upload & Publish
                            </button>
                        </div>
                        
                        <div class="button-group" id="useDraftButtonGroup" style="display: none;">
                            <button type="button" class="btn btn-secondary" onclick="cancelUseDraft()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-success" onclick="publishDraft()">
                                <i class="fas fa-paper-plane"></i> Publish Draft
                            </button>
                        </div>
                        
                        <div class="button-group" id="editDraftButtonGroup" style="display: none;">
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-success" onclick="updateDraft()">
                                <i class="fas fa-save"></i> Update Draft
                            </button>
                        </div>
                    </form>
                </div>

                <div class="draft-section">
                    <div class="section-header">
                        <div class="section-title">Draft Banners</div>
                        <div class="section-actions">
                            <span class="section-badge" id="draftCount">0 Drafts</span>
                        </div>
                    </div>
                    
                    <div class="draft-banners">
                        <div class="draft-list" id="draftList">
                            <div class="no-drafts">
                                <i class="fas fa-file-alt" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
                                <p>No draft banners saved</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>ServiceCo App Management &copy; <?php echo date('Y'); ?>. All rights reserved.</p>
        </div>
    </div>

    <div class="modal delete-modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Banner</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="confirmation-message">Are you sure you want to delete this banner?</div>
                <div class="warning-text">
                    <i class="fas fa-info-circle"></i> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <div class="modal preview-modal" id="previewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Banner Preview</h3>
                <button class="modal-close" onclick="closeModal('previewModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-image-container">
                    <img class="modal-image" id="modalPreviewImage" alt="Banner Preview">
                </div>
                <div class="file-info-grid">
                    <div class="file-info-item">
                        <div class="file-info-label">File Name</div>
                        <div class="file-info-value" id="modalFileName"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Campaign Title</div>
                        <div class="file-info-value" id="modalCampaignTitle"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Expiry Date</div>
                        <div class="file-info-value" id="modalExpiryDate"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Dimensions</div>
                        <div class="file-info-value" id="modalDimensions"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">File Size</div>
                        <div class="file-info-value" id="modalFileSize"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Status</div>
                        <div class="file-info-value" id="modalStatus"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Upload Date</div>
                        <div class="file-info-value" id="modalUploadDate"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Clicks</div>
                        <div class="file-info-value" id="modalClicks"></div>
                    </div>
                    <div class="file-info-item">
                        <div class="file-info-label">Redirect Link</div>
                        <div class="file-info-value" id="modalRedirectLink"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('previewModal')">
                    <i class="fas fa-times"></i> Close Preview
                </button>
            </div>
        </div>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="notification-message success" id="successMessage"></div>
    <div class="notification-message error" id="errorMessage"></div>
    <div class="notification-message warning" id="warningMessage"></div>

    <script>
        const database = window.database;
        const storage = window.storage;

        let allBanners = [];
        let currentBannerId = null;
        let currentBannerTitle = null;
        let currentPreviewImage = null;
        let pendingUpload = null;
        let currentFilter = 'active';
        
        let draggedItem = null;
        let draggedItemId = null;
        let dragStartY = 0;
        let dragStartTime = 0;

        const bannersRef = database.ref('banners');

        function adjustContentPosition() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.app-content');
            
            if (!sidebar || !content) return;
            
            const isAutoHide = sidebar.classList.contains('auto-hide');
            const isCollapsed = sidebar.classList.contains('collapsed');
            const isHovered = sidebar.matches(':hover') && isAutoHide;
            
            let sidebarWidth;
            if (isAutoHide) {
                sidebarWidth = isHovered ? 240 : 70;
            } else {
                sidebarWidth = isCollapsed ? 70 : 240;
            }
            
            content.style.marginLeft = sidebarWidth + 'px';
            content.style.width = `calc(100% - ${sidebarWidth}px)`;
        }

        window.adjustContentPosition = adjustContentPosition;

        function setDefaultExpiryDate() {
            const today = new Date();
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            const expiryInput = document.getElementById('expiryDate');
            if (expiryInput) {
                expiryInput.value = nextWeek.toISOString().split('T')[0];
            }
        }

        function setupEventListeners() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('bannerFile');

            if (uploadArea) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    
                    if (e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        handleFileSelect({ target: fileInput });
                    }
                });
            }

            const form = document.getElementById('bannerUploadForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await processFormSubmission();
                });
            }

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this.id);
                    }
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllModals();
                }
            });
        }

        function loadBanners() {
            showLoading(true);
            
            bannersRef.once('value')
                .then(snapshot => {
                    allBanners = [];
                    const banners = snapshot.val();
                    
                    if (banners) {
                        Object.keys(banners).forEach(key => {
                            const banner = banners[key];
                            banner.id = key;
                            allBanners.push(banner);
                        });
                    }
                    
                    updateUI();
                    showLoading(false);
                })
                .catch(error => {
                    console.error('Error loading banners:', error);
                    showNotification('Error loading banners: ' + error.message, 'error');
                    showLoading(false);
                });
        }

        function setupRealtimeListeners() {
            bannersRef.on('child_added', (snapshot) => {
                const banner = snapshot.val();
                banner.id = snapshot.key;
                
                const existingIndex = allBanners.findIndex(b => b.id === banner.id);
                if (existingIndex === -1) {
                    allBanners.push(banner);
                    updateUI();
                }
            });

            bannersRef.on('child_changed', (snapshot) => {
                const updatedBanner = snapshot.val();
                updatedBanner.id = snapshot.key;
                
                const index = allBanners.findIndex(b => b.id === updatedBanner.id);
                if (index !== -1) {
                    allBanners[index] = updatedBanner;
                    updateUI();
                }
            });

            bannersRef.on('child_removed', (snapshot) => {
                const bannerId = snapshot.key;
                
                const index = allBanners.findIndex(b => b.id === bannerId);
                if (index !== -1) {
                    allBanners.splice(index, 1);
                    updateUI();
                }
            });
        }

        function updateUI() {
            updateBannersGrid();
            updateOrderList();
            updateDraftList();
            updateCounts();
        }

        function updateBannersGrid() {
            const bannersGrid = document.getElementById('bannersGrid');
            if (!bannersGrid) return;
            
            let filteredBanners = [];
            if (currentFilter === 'active') {
                filteredBanners = allBanners.filter(b => b.status === 'active');
            } else if (currentFilter === 'inactive') {
                filteredBanners = allBanners.filter(b => b.status === 'inactive');
            } else if (currentFilter === 'all') {
                filteredBanners = allBanners.filter(b => b.status === 'active' || b.status === 'inactive');
            }
            
            filteredBanners.sort((a, b) => {
                if (a.status === 'active' && b.status === 'active') {
                    return (a.display_order || 0) - (b.display_order || 0);
                }
                return 0;
            });
            
            bannersGrid.innerHTML = '';
            
            if (filteredBanners.length === 0) {
                bannersGrid.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="empty-text">No ${currentFilter} banners found</div>
                        <div class="empty-text" style="font-size: 12px; margin-top: 5px;">Upload your first banner on the right</div>
                    </div>
                `;
                return;
            }
            
            filteredBanners.forEach(banner => {
                const bannerCard = createBannerCard(banner);
                bannersGrid.appendChild(bannerCard);
            });
            
            if (currentFilter === 'active') {
                initializeDragAndDrop();
            }
        }

        function createBannerCard(banner) {
            const card = document.createElement('div');
            card.className = 'banner-card';
            card.dataset.id = banner.id;
            card.dataset.order = banner.display_order || 0;
            
            if (banner.status === 'active') {
                card.setAttribute('draggable', 'true');
            } else {
                card.setAttribute('draggable', 'false');
            }
            
            const statusText = banner.status === 'active' ? `Order: ${banner.display_order || 'N/A'}` : 'Inactive';
            const statusClass = banner.status;
            const statusIcon = banner.status === 'active' ? 'fa-pause' : 'fa-play';
            const statusAction = banner.status === 'active' ? 'inactive' : 'active';
            const statusTitle = banner.status === 'active' ? 'Deactivate' : 'Activate';
            
            card.innerHTML = `
                <div class="banner-image">
                    <img src="${banner.imageUrl || 'https://via.placeholder.com/1080x540/3b82f6/ffffff?text=' + encodeURIComponent(banner.title.substring(0, 20))}" alt="${banner.title}">
                    <div class="banner-overlay">
                        <div class="banner-actions">
                            <button class="banner-action-btn" onclick="event.stopPropagation(); viewBannerPreview('${banner.id}')" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="banner-action-btn" onclick="event.stopPropagation(); toggleBannerStatus('${banner.id}', '${statusAction}')" title="${statusTitle}">
                                <i class="fas ${statusIcon}"></i>
                            </button>
                            <button class="banner-action-btn" onclick="event.stopPropagation(); showDeleteModal('${banner.id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="banner-info">
                    <div class="banner-title">${banner.title}</div>
                    <div class="banner-meta">
                        <span>${statusText}</span>
                        <span class="status-badge ${statusClass}">${banner.status.charAt(0).toUpperCase() + banner.status.slice(1)}</span>
                    </div>
                </div>
            `;
            
            card.onclick = () => viewBannerPreview(banner.id);
            
            return card;
        }

        function initializeDragAndDrop() {
            const draggables = document.querySelectorAll('.banner-card[draggable="true"]');
            
            draggables.forEach(draggable => {
                draggable.removeEventListener('dragstart', handleDragStart);
                draggable.removeEventListener('dragend', handleDragEnd);
                draggable.removeEventListener('dragover', handleDragOver);
                draggable.removeEventListener('dragenter', handleDragEnter);
                draggable.removeEventListener('dragleave', handleDragLeave);
                draggable.removeEventListener('drop', handleDrop);
                
                draggable.addEventListener('dragstart', handleDragStart);
                draggable.addEventListener('dragend', handleDragEnd);
                draggable.addEventListener('dragover', handleDragOver);
                draggable.addEventListener('dragenter', handleDragEnter);
                draggable.addEventListener('dragleave', handleDragLeave);
                draggable.addEventListener('drop', handleDrop);
            });
        }

        function handleDragStart(e) {
            draggedItem = this;
            draggedItemId = this.dataset.id;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
            dragStartY = e.clientY;
            dragStartTime = Date.now();
            
            document.body.style.userSelect = 'none';
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }

        function handleDragEnter(e) {
            e.preventDefault();
            if (this !== draggedItem) {
                this.classList.add('drag-over');
            }
        }

        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            document.querySelectorAll('.banner-card').forEach(card => {
                card.classList.remove('drag-over');
            });
            document.body.style.userSelect = '';
            draggedItem = null;
            draggedItemId = null;
        }

        async function handleDrop(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (!draggedItem || this === draggedItem) return;
            
            const dropTarget = this;
            const dropTargetId = dropTarget.dataset.id;
            
            const activeBanners = allBanners.filter(b => b.status === 'active')
                .sort((a, b) => (a.display_order || 0) - (b.display_order || 0));
            
            const draggedBanner = activeBanners.find(b => b.id === draggedItemId);
            const dropBanner = activeBanners.find(b => b.id === dropTargetId);
            
            if (!draggedBanner || !dropBanner) return;
            
            const draggedOrder = draggedBanner.display_order || 0;
            const dropOrder = dropBanner.display_order || 0;
            
            const updates = {};
            
            activeBanners.forEach(banner => {
                if (banner.id === draggedBanner.id) {
                    updates[`${banner.id}/display_order`] = dropOrder;
                    updates[`${banner.id}/updated_at`] = new Date().toISOString();
                } else if (banner.id === dropBanner.id) {
                    updates[`${banner.id}/display_order`] = draggedOrder;
                    updates[`${banner.id}/updated_at`] = new Date().toISOString();
                }
            });
            
            try {
                await bannersRef.update(updates);
                showNotification('Banner order updated!', 'success');
            } catch (error) {
                console.error('Error updating banner order:', error);
                showNotification('Error updating banner order: ' + error.message, 'error');
            }
        }

        function toggleOrderSection() {
            const orderContent = document.getElementById('orderContent');
            const orderToggle = document.querySelector('.order-toggle i');
            
            if (orderContent && orderToggle) {
                orderContent.classList.toggle('collapsed');
                orderToggle.classList.toggle('fa-chevron-down');
                orderToggle.classList.toggle('fa-chevron-right');
            }
        }

        function updateOrderList() {
            const orderManagement = document.getElementById('orderManagement');
            const orderList = document.getElementById('orderList');
            
            if (!orderManagement || !orderList) return;
            
            const activeBanners = allBanners.filter(b => b.status === 'active')
                .sort((a, b) => (a.display_order || 0) - (b.display_order || 0));
            
            if (activeBanners.length > 0) {
                orderManagement.style.display = 'block';
            } else {
                orderManagement.style.display = 'none';
                return;
            }
            
            orderList.innerHTML = '';
            
            activeBanners.forEach(banner => {
                const orderItem = createOrderItem(banner);
                orderList.appendChild(orderItem);
            });
        }

        function createOrderItem(banner) {
            const item = document.createElement('div');
            item.className = 'order-item';
            item.dataset.id = banner.id;
            
            const maxOrder = allBanners.filter(b => b.status === 'active').length;
            
            item.innerHTML = `
                <div class="order-info">
                    <div class="order-number">${banner.display_order || 'N/A'}</div>
                    <div class="order-text">
                        <div class="order-banner-title">${banner.title}</div>
                        <div class="order-banner-status">Active</div>
                    </div>
                </div>
                <div class="order-actions">
                    <select class="order-dropdown" onchange="updateBannerOrder('${banner.id}', this.value)">
                        ${generateOrderOptions(maxOrder, banner.display_order)}
                    </select>
                    <button class="order-btn" onclick="moveBannerUp('${banner.id}')" title="Move Up" ${banner.display_order <= 1 ? 'disabled' : ''}>
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button class="order-btn" onclick="moveBannerDown('${banner.id}')" title="Move Down" ${banner.display_order >= maxOrder ? 'disabled' : ''}>
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            `;
            
            return item;
        }

        function updateDraftList() {
            const draftList = document.getElementById('draftList');
            if (!draftList) return;
            
            const draftBanners = allBanners.filter(b => b.status === 'draft');
            
            draftList.innerHTML = '';
            
            if (draftBanners.length === 0) {
                draftList.innerHTML = `
                    <div class="no-drafts">
                        <i class="fas fa-file-alt" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
                        <p>No draft banners saved</p>
                    </div>
                `;
                return;
            }
            
            draftBanners.forEach(draft => {
                const draftItem = createDraftItem(draft);
                draftList.appendChild(draftItem);
            });
        }

        function createDraftItem(draft) {
            const item = document.createElement('div');
            item.className = 'draft-item';
            item.dataset.id = draft.id;
            item.onclick = () => useDraft(draft.id);
            
            item.innerHTML = `
                <div class="draft-info">
                    <div class="draft-image">
                        <img src="${draft.imageUrl || 'https://via.placeholder.com/1080x540/6b7280/ffffff?text=' + encodeURIComponent(draft.title.substring(0, 10))}" alt="${draft.title}">
                    </div>
                    <div class="draft-text">
                        <div class="draft-title">${draft.title}</div>
                        <div class="draft-date">Saved: ${formatDate(draft.created_at) || 'N/A'}</div>
                    </div>
                </div>
                <div class="draft-actions">
                    <button class="draft-btn" onclick="event.stopPropagation(); editDraft('${draft.id}')" title="Edit Draft">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="draft-btn" onclick="event.stopPropagation(); showDeleteModal('${draft.id}')" title="Delete Draft">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            return item;
        }

        function updateCounts() {
            const activeCount = allBanners.filter(b => b.status === 'active').length;
            const draftCount = allBanners.filter(b => b.status === 'draft').length;
            const inactiveCount = allBanners.filter(b => b.status === 'inactive').length;
            
            const bannerCountEl = document.getElementById('bannerCount');
            if (bannerCountEl) {
                if (currentFilter === 'all') {
                    bannerCountEl.textContent = `${activeCount + inactiveCount} Banners`;
                } else if (currentFilter === 'active') {
                    bannerCountEl.textContent = `${activeCount} Active`;
                } else {
                    bannerCountEl.textContent = `${inactiveCount} Inactive`;
                }
            }
            
            const draftCountEl = document.getElementById('draftCount');
            if (draftCountEl) {
                draftCountEl.textContent = `${draftCount} Draft${draftCount !== 1 ? 's' : ''}`;
            }
        }

        function filterBanners(status) {
            currentFilter = status;
            
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            updateBannersGrid();
            updateCounts();
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.match('image.*')) {
                showNotification('Please select an image file (JPEG, PNG, GIF, WebP)', 'error');
                resetFileInput();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showNotification('File size should be less than 5MB', 'error');
                resetFileInput();
                return;
            }

            const img = new Image();
            const reader = new FileReader();
            
            reader.onload = function(e) {
                img.onload = function() {
                    const width = this.width;
                    const height = this.height;
                    
                    const preview = document.getElementById('uploadPreview');
                    const previewImage = document.getElementById('previewImage');
                    const previewFileName = document.getElementById('previewFileName');
                    const previewFileSize = document.getElementById('previewFileSize');
                    const previewDimensions = document.getElementById('previewDimensions');

                    if (preview && previewImage && previewFileName && previewFileSize && previewDimensions) {
                        previewFileName.textContent = file.name;
                        previewFileSize.textContent = formatFileSize(file.size);
                        previewDimensions.textContent = `${width}×${height}px`;
                        
                        if (width !== 1080 || height !== 540) {
                            previewDimensions.style.color = '#f59e0b';
                            previewDimensions.style.fontWeight = 'bold';
                        } else {
                            previewDimensions.style.color = '#10b981';
                            previewDimensions.style.fontWeight = 'bold';
                        }

                        previewImage.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    
                    currentPreviewImage = {
                        src: e.target.result,
                        name: file.name,
                        size: file.size,
                        dimensions: { width, height },
                        file: file
                    };
                };
                img.src = e.target.result;
            };
            
            reader.readAsDataURL(file);
        }

        function resetFileInput() {
            document.getElementById('bannerFile').value = '';
            resetPreview();
        }

        function resetPreview() {
            const preview = document.getElementById('uploadPreview');
            if (preview) {
                preview.style.display = 'none';
            }
            currentPreviewImage = null;
            pendingUpload = null;
        }

        async function processFormSubmission() {
            const title = document.getElementById('bannerTitle').value.trim();
            const file = document.getElementById('bannerFile').files[0];
            const redirectLink = document.getElementById('redirectLink').value.trim();
            const expiryDate = document.getElementById('expiryDate').value;
            const bannerId = document.getElementById('currentBannerId').value;

            if (!title) {
                showNotification('Please enter a campaign title', 'error');
                return;
            }

            if (!file && !currentPreviewImage) {
                showNotification('Please select an image file to upload', 'error');
                return;
            }

            if (!expiryDate) {
                showNotification('Please select an expiry date', 'error');
                return;
            }

            await createBanner(title, file, redirectLink, expiryDate, 'active');
        }

        async function createBanner(title, file, redirectLink, expiryDate, status) {
            showLoading(true);
            
            try {
                const bannerId = bannersRef.push().key;
                
                let imageUrl = '';
                if (file) {
                    imageUrl = await uploadImageToStorage(file, bannerId);
                }
                
                let displayOrder = null;
                if (status === 'active') {
                    const activeBanners = allBanners.filter(b => b.status === 'active');
                    displayOrder = activeBanners.length + 1;
                }
                
                const bannerData = {
                    title: title,
                    status: status,
                    display_order: displayOrder,
                    created_at: new Date().toISOString(),
                    expiry_date: expiryDate,
                    redirect_link: redirectLink || '',
                    clicks: 0,
                    file_size: file ? formatFileSize(file.size) : 'Unknown',
                    dimensions: currentPreviewImage ? `${currentPreviewImage.dimensions.width}×${currentPreviewImage.dimensions.height}` : '1080×540',
                    imageUrl: imageUrl,
                    updated_at: new Date().toISOString()
                };
                
                await bannersRef.child(bannerId).set(bannerData);
                
                resetForm();
                
                if (status === 'active') {
                    showNotification(`Banner "${title}" published successfully!`, 'success');
                } else {
                    showNotification(`Banner "${title}" saved as draft!`, 'success');
                }
                
            } catch (error) {
                console.error('Error saving banner:', error);
                showNotification('Error saving banner: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        async function updateBanner(bannerId, title, file, redirectLink, expiryDate, status) {
            showLoading(true);
            
            try {
                const bannerRef = bannersRef.child(bannerId);
                const snapshot = await bannerRef.once('value');
                const currentBanner = snapshot.val();
                
                if (!currentBanner) {
                    throw new Error('Banner not found');
                }
                
                let imageUrl = currentBanner.imageUrl;
                if (file) {
                    imageUrl = await uploadImageToStorage(file, bannerId);
                    
                    if (currentBanner.imageUrl && currentBanner.imageUrl !== imageUrl) {
                        try {
                            const oldImageRef = storage.refFromURL(currentBanner.imageUrl);
                            await oldImageRef.delete();
                        } catch (deleteError) {
                            console.warn('Could not delete old image:', deleteError);
                        }
                    }
                }
                
                const updates = {
                    title: title,
                    expiry_date: expiryDate,
                    redirect_link: redirectLink || '',
                    imageUrl: imageUrl,
                    updated_at: new Date().toISOString()
                };
                
                if (status) {
                    updates.status = status;
                    if (status === 'active') {
                        const activeBanners = allBanners.filter(b => b.status === 'active');
                        updates.display_order = activeBanners.length + 1;
                    } else {
                        updates.display_order = null;
                    }
                }
                
                if (file) {
                    updates.file_size = formatFileSize(file.size);
                    updates.dimensions = currentPreviewImage ? 
                        `${currentPreviewImage.dimensions.width}×${currentPreviewImage.dimensions.height}` : 
                        '1080×540';
                }
                
                await bannerRef.update(updates);
                
                resetForm();
                
                showNotification(`Banner "${title}" updated successfully!`, 'success');
                
            } catch (error) {
                console.error('Error updating banner:', error);
                showNotification('Error updating banner: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        async function uploadImageToStorage(file, bannerId) {
            return new Promise((resolve, reject) => {
                const uploadStatus = document.getElementById('uploadStatus');
                if (uploadStatus) {
                    uploadStatus.textContent = 'Uploading image...';
                    uploadStatus.className = 'upload-status uploading';
                    uploadStatus.style.display = 'block';
                }
                
                const storageRef = storage.ref();
                const imageRef = storageRef.child(`banners/${bannerId}/${file.name}`);
                
                const uploadTask = imageRef.put(file);
                
                uploadTask.on('state_changed',
                    (snapshot) => {
                        const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                        if (uploadStatus) {
                            uploadStatus.textContent = `Uploading: ${Math.round(progress)}%`;
                        }
                    },
                    (error) => {
                        if (uploadStatus) {
                            uploadStatus.className = 'upload-status error';
                            uploadStatus.textContent = 'Upload failed';
                        }
                        reject(error);
                    },
                    async () => {
                        const downloadURL = await uploadTask.snapshot.ref.getDownloadURL();
                        
                        if (uploadStatus) {
                            uploadStatus.className = 'upload-status success';
                            uploadStatus.textContent = 'Upload complete!';
                            
                            setTimeout(() => {
                                uploadStatus.style.display = 'none';
                            }, 2000);
                        }
                        
                        resolve(downloadURL);
                    }
                );
            });
        }

        async function saveAsDraft() {
            const title = document.getElementById('bannerTitle').value.trim();
            const file = document.getElementById('bannerFile').files[0];
            const redirectLink = document.getElementById('redirectLink').value.trim();
            const expiryDate = document.getElementById('expiryDate').value;

            if (!title) {
                showNotification('Please enter a campaign title', 'error');
                return;
            }

            if (!file && !currentPreviewImage) {
                showNotification('Please select an image file', 'error');
                return;
            }

            if (!expiryDate) {
                showNotification('Please select an expiry date', 'error');
                return;
            }

            await createBanner(title, file, redirectLink, expiryDate, 'draft');
        }

        function useDraft(draftId) {
            const draft = allBanners.find(b => b.id === draftId && b.status === 'draft');
            if (!draft) {
                showNotification('Draft not found', 'error');
                return;
            }
            
            document.getElementById('currentBannerId').value = draft.id;
            document.getElementById('currentBannerStatus').value = 'use';
            document.getElementById('bannerTitle').value = draft.title;
            document.getElementById('redirectLink').value = draft.redirect_link || '';
            document.getElementById('expiryDate').value = draft.expiry_date;
            
            document.getElementById('uploadButtonGroup').style.display = 'none';
            document.getElementById('useDraftButtonGroup').style.display = 'flex';
            document.getElementById('editDraftButtonGroup').style.display = 'none';
            
            if (draft.imageUrl) {
                const preview = document.getElementById('uploadPreview');
                const previewImage = document.getElementById('previewImage');
                const previewFileName = document.getElementById('previewFileName');
                const previewFileSize = document.getElementById('previewFileSize');
                const previewDimensions = document.getElementById('previewDimensions');
                
                if (preview && previewImage && previewFileName && previewFileSize && previewDimensions) {
                    previewImage.src = draft.imageUrl;
                    previewFileName.textContent = 'Current Image';
                    previewFileSize.textContent = draft.file_size || 'Unknown';
                    previewDimensions.textContent = draft.dimensions || '1080×540';
                    preview.style.display = 'block';
                }
            }
            
            showNotification('Draft loaded. Click "Publish Draft" to publish it.', 'success');
        }

        async function publishDraft() {
            const bannerId = document.getElementById('currentBannerId').value;
            const title = document.getElementById('bannerTitle').value.trim();
            const redirectLink = document.getElementById('redirectLink').value.trim();
            const expiryDate = document.getElementById('expiryDate').value;
            
            await updateBanner(bannerId, title, null, redirectLink, expiryDate, 'active');
        }

        function editDraft(draftId) {
            const draft = allBanners.find(b => b.id === draftId && b.status === 'draft');
            if (!draft) {
                showNotification('Draft not found', 'error');
                return;
            }
            
            document.getElementById('currentBannerId').value = draft.id;
            document.getElementById('currentBannerStatus').value = 'editing';
            document.getElementById('bannerTitle').value = draft.title;
            document.getElementById('redirectLink').value = draft.redirect_link || '';
            document.getElementById('expiryDate').value = draft.expiry_date;
            
            document.getElementById('uploadButtonGroup').style.display = 'none';
            document.getElementById('useDraftButtonGroup').style.display = 'none';
            document.getElementById('editDraftButtonGroup').style.display = 'flex';
            
            if (draft.imageUrl) {
                const preview = document.getElementById('uploadPreview');
                const previewImage = document.getElementById('previewImage');
                const previewFileName = document.getElementById('previewFileName');
                const previewFileSize = document.getElementById('previewFileSize');
                const previewDimensions = document.getElementById('previewDimensions');
                
                if (preview && previewImage && previewFileName && previewFileSize && previewDimensions) {
                    previewImage.src = draft.imageUrl;
                    previewFileName.textContent = 'Current Image';
                    previewFileSize.textContent = draft.file_size || 'Unknown';
                    previewDimensions.textContent = draft.dimensions || '1080×540';
                    preview.style.display = 'block';
                }
            }
            
            showNotification('Draft loaded for editing.', 'success');
        }

        async function updateDraft() {
            const bannerId = document.getElementById('currentBannerId').value;
            const title = document.getElementById('bannerTitle').value.trim();
            const file = document.getElementById('bannerFile').files[0];
            const redirectLink = document.getElementById('redirectLink').value.trim();
            const expiryDate = document.getElementById('expiryDate').value;
            
            await updateBanner(bannerId, title, file, redirectLink, expiryDate, 'draft');
        }

        function cancelUseDraft() {
            resetForm();
            showNotification('Cancelled', 'warning');
        }

        function cancelEdit() {
            resetForm();
            showNotification('Edit cancelled', 'warning');
        }

        function resetForm() {
            document.getElementById('bannerUploadForm').reset();
            document.getElementById('currentBannerId').value = '';
            document.getElementById('currentBannerStatus').value = 'new';
            resetPreview();
            setDefaultExpiryDate();
            
            document.getElementById('uploadButtonGroup').style.display = 'flex';
            document.getElementById('useDraftButtonGroup').style.display = 'none';
            document.getElementById('editDraftButtonGroup').style.display = 'none';
            
            const uploadStatus = document.getElementById('uploadStatus');
            if (uploadStatus) {
                uploadStatus.style.display = 'none';
            }
        }

        async function toggleBannerStatus(bannerId, newStatus) {
            try {
                const bannerRef = bannersRef.child(bannerId);
                
                const snapshot = await bannerRef.once('value');
                const banner = snapshot.val();
                
                if (!banner) {
                    throw new Error('Banner not found');
                }
                
                const updates = {
                    status: newStatus,
                    updated_at: new Date().toISOString()
                };
                
                if (newStatus === 'active') {
                    const activeBanners = allBanners.filter(b => b.status === 'active');
                    updates.display_order = activeBanners.length + 1;
                } else {
                    updates.display_order = null;
                }
                
                await bannerRef.update(updates);
                
                showNotification(`Banner ${newStatus === 'active' ? 'activated' : 'deactivated'}!`, 'success');
                
            } catch (error) {
                console.error('Error updating banner status:', error);
                showNotification('Error updating banner status: ' + error.message, 'error');
            }
        }

        function showDeleteModal(bannerId) {
            currentBannerId = bannerId;
            showModal('deleteModal');
        }

        async function confirmDelete() {
            showLoading(true);
            
            try {
                const bannerRef = bannersRef.child(currentBannerId);
                
                const snapshot = await bannerRef.once('value');
                const banner = snapshot.val();
                
                if (banner && banner.imageUrl) {
                    try {
                        const imageRef = storage.refFromURL(banner.imageUrl);
                        await imageRef.delete();
                    } catch (deleteError) {
                        console.warn('Could not delete image from storage:', deleteError);
                    }
                }
                
                await bannerRef.remove();
                
                showNotification(`Banner deleted successfully!`, 'success');
                closeModal('deleteModal');
                
                if (document.getElementById('currentBannerId').value === currentBannerId) {
                    resetForm();
                }
                
            } catch (error) {
                console.error('Error deleting banner:', error);
                showNotification('Error deleting: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        async function updateBannerOrder(bannerId, newOrder) {
            newOrder = parseInt(newOrder);
            
            try {
                const banner = allBanners.find(b => b.id === bannerId && b.status === 'active');
                if (!banner) return;
                
                const oldOrder = banner.display_order || 0;
                if (newOrder === oldOrder) return;
                
                const updates = {};
                const activeBanners = allBanners.filter(b => b.status === 'active')
                    .sort((a, b) => (a.display_order || 0) - (b.display_order || 0));
                
                activeBanners.forEach((b, index) => {
                    if (b.id === bannerId) {
                        updates[`${b.id}/display_order`] = newOrder;
                    } else {
                        let adjustedOrder = b.display_order || 0;
                        
                        if (newOrder > oldOrder) {
                            if (adjustedOrder > oldOrder && adjustedOrder <= newOrder) {
                                adjustedOrder--;
                            }
                        } else {
                            if (adjustedOrder >= newOrder && adjustedOrder < oldOrder) {
                                adjustedOrder++;
                            }
                        }
                        
                        updates[`${b.id}/display_order`] = adjustedOrder;
                    }
                    
                    updates[`${b.id}/updated_at`] = new Date().toISOString();
                });
                
                await bannersRef.update(updates);
                
                showNotification('Display order updated!', 'success');
                
            } catch (error) {
                console.error('Error updating display order:', error);
                showNotification('Error updating display order: ' + error.message, 'error');
            }
        }

        async function moveBannerUp(bannerId) {
            const banner = allBanners.find(b => b.id === bannerId && b.status === 'active');
            if (!banner || !banner.display_order || banner.display_order <= 1) return;
            
            await updateBannerOrder(bannerId, banner.display_order - 1);
        }

        async function moveBannerDown(bannerId) {
            const banner = allBanners.find(b => b.id === bannerId && b.status === 'active');
            if (!banner || !banner.display_order) return;
            
            const maxOrder = Math.max(...allBanners.filter(b => b.status === 'active').map(b => b.display_order || 0));
            if (banner.display_order >= maxOrder) return;
            
            await updateBannerOrder(bannerId, banner.display_order + 1);
        }

        function viewBannerPreview(bannerId) {
            const banner = allBanners.find(b => b.id === bannerId);
            if (!banner) {
                showNotification('Banner not found', 'error');
                return;
            }

            const modalPreviewImage = document.getElementById('modalPreviewImage');
            const modalFileName = document.getElementById('modalFileName');
            const modalCampaignTitle = document.getElementById('modalCampaignTitle');
            const modalExpiryDate = document.getElementById('modalExpiryDate');
            const modalDimensions = document.getElementById('modalDimensions');
            const modalFileSize = document.getElementById('modalFileSize');
            const modalStatus = document.getElementById('modalStatus');
            const modalUploadDate = document.getElementById('modalUploadDate');
            const modalClicks = document.getElementById('modalClicks');
            const modalRedirectLink = document.getElementById('modalRedirectLink');

            if (modalPreviewImage) {
                modalPreviewImage.src = banner.imageUrl || 'https://via.placeholder.com/1080x540/3b82f6/ffffff?text=' + encodeURIComponent(banner.title);
            }
            if (modalFileName) modalFileName.textContent = banner.title + ' Banner';
            if (modalCampaignTitle) modalCampaignTitle.textContent = banner.title;
            if (modalExpiryDate) modalExpiryDate.textContent = banner.expiry_date || 'Not set';
            if (modalDimensions) modalDimensions.textContent = banner.dimensions || '1080×540';
            if (modalFileSize) modalFileSize.textContent = banner.file_size || 'Unknown';
            if (modalStatus) modalStatus.textContent = banner.status.charAt(0).toUpperCase() + banner.status.slice(1);
            if (modalUploadDate) modalUploadDate.textContent = formatDate(banner.created_at) || 'Unknown';
            if (modalClicks) modalClicks.textContent = banner.clicks ? banner.clicks.toLocaleString() : '0';
            if (modalRedirectLink) modalRedirectLink.textContent = banner.redirect_link || 'None';
            
            showModal('previewModal');
        }

        function generateOrderOptions(count, selected) {
            let options = '';
            for (let i = 1; i <= count; i++) {
                options += `<option value="${i}" ${i === selected ? 'selected' : ''}>${i}</option>`;
            }
            return options;
        }

        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') return 'Unknown';
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        function closeAllModals() {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
            document.body.style.overflow = 'auto';
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = show ? 'flex' : 'none';
            }
        }

        function showNotification(message, type) {
            const notification = document.getElementById(type + 'Message');
            if (notification) {
                notification.textContent = message;
                notification.style.display = 'block';
                
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setDefaultExpiryDate();
            setupEventListeners();
            loadBanners();
            setupRealtimeListeners();
            
            setTimeout(() => adjustContentPosition(), 100);
        });

        window.filterBanners = filterBanners;
        window.toggleOrderSection = toggleOrderSection;
        window.handleFileSelect = handleFileSelect;
        window.saveAsDraft = saveAsDraft;
        window.cancelUseDraft = cancelUseDraft;
        window.cancelEdit = cancelEdit;
        window.publishDraft = publishDraft;
        window.updateDraft = updateDraft;
        window.toggleBannerStatus = toggleBannerStatus;
        window.showDeleteModal = showDeleteModal;
        window.confirmDelete = confirmDelete;
        window.viewBannerPreview = viewBannerPreview;
        window.updateBannerOrder = updateBannerOrder;
        window.moveBannerUp = moveBannerUp;
        window.moveBannerDown = moveBannerDown;
        window.closeModal = closeModal;
        window.closeAllModals = closeAllModals;
    </script>
</body>
</html>