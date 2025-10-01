@extends('admin.layout')
@section('title','ข่าวสาร')
@section('page_title','ข่าวสาร')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl border p-6">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-bold">ข่าวสาร</h1>
    <a href="{{ route('admin.news.create') }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm">+ เพิ่มข่าว</a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-lg bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
  @endif

  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead><tr class="border-b text-gray-500">
        <th class="py-2 pr-4 text-left">หัวข้อ</th>
        <th class="py-2 pr-4 text-left">หมวดหมู่</th>
        <th class="py-2 pr-4 text-left">สถานะ</th>
        <th class="py-2 pr-4 text-left">เผยแพร่เมื่อ</th>
        <th class="py-2 pr-0 text-right">จัดการ</th>
      </tr></thead>
      <tbody>
      @forelse($news as $n)
        <tr class="border-b last:border-0">
          <td class="py-2 pr-4">{{ $n->title }}</td>
          <td class="py-2 pr-4">{{ $n->category }}</td>
          <td class="py-2 pr-4">{{ $n->status === 'published' ? 'เผยแพร่' : 'ฉบับร่าง' }}</td>
          <td class="py-2 pr-4">{{ optional($n->published_at)->format('d/m/Y H:i') ?? '-' }}</td>
          <td class="py-2 pr-0 text-right">
            <a href="{{ route('admin.news.edit',$n) }}" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 mr-2">แก้ไข</a>
            <form action="{{ route('admin.news.destroy',$n) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันลบข่าวนี้?')">
              @csrf @method('DELETE')
              <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">ลบ</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="py-4 text-gray-500">ยังไม่มีข่าว</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $news->links() }}</div>
</div>

@endsection
