@php($item = $item ?? null)
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Ten co che <span style="color:red">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name ?? '') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>Co che cha</label>
            <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                <option value="">Khong co cha</option>
                @foreach($parents ?? [] as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $item->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>Trang hoat dong <span style="color:red">*</span></label>
            <select name="trang_hoat_dong" class="form-control @error('trang_hoat_dong') is-invalid @enderror" required>
                <option value="combo" {{ old('trang_hoat_dong', $item->trang_hoat_dong ?? 'combo') === 'combo' ? 'selected' : '' }}>Combo</option>
                <option value="tu_chon" {{ old('trang_hoat_dong', $item->trang_hoat_dong ?? 'combo') === 'tu_chon' ? 'selected' : '' }}>Tu chon</option>
            </select>
            @error('trang_hoat_dong')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    @for($i = 1; $i <= 6; $i++)
        <div class="col-4">
            <div class="form-group">
                <label>Tang {{ $i }} (%)</label>
                <input type="number" step="0.01" min="0" name="tang_{{ $i }}" class="form-control @error('tang_'.$i) is-invalid @enderror" value="{{ old('tang_'.$i, $item->{'tang_'.$i} ?? 0) }}">
                @error('tang_'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    @endfor
</div>
