@extends('errors.minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('description', 'Too many attempts. Please wait a minute and try again.')
